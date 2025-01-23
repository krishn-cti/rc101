<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GoogleCourse;
use App\Models\GoogleAssignment;
use App\Models\GoogleCourseParticipant;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Classroom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client as GuzzleClient;

class GoogleClassroomController extends Controller
{
    private $client;
    private $classroomService;

    public function __construct()
    {
        // Initialize Google Client
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path(env('GOOGLE_CREDENTIALS_PATH')));
        $this->client->addScope(Classroom::CLASSROOM_COURSES);
        $this->client->addScope(Classroom::CLASSROOM_ROSTERS);
        $this->client->addScope(Classroom::CLASSROOM_COURSEWORK_STUDENTS);
        $this->client->addScope(Classroom::CLASSROOM_ROSTERS_READONLY);
        $this->client->addScope(Classroom::CLASSROOM_PROFILE_EMAILS);
        // $this->client->addScope(Classroom::CLASSROOM_INVITATIONS);
        // $this->client->addScope(Classroom::CLASSROOM_COURSEWORK_ME);
        // $this->client->addScope(Classroom::CLASSROOM_ANNOUNCEMENTS);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    // get the list of courses
    // public function listCourses(Request $request)
    // {
    //     try {
    //         $accessToken = $request->bearerToken();

    //         if (!$accessToken) {
    //             return response()->json(['error' => 'Google token not found'], 400);
    //         }

    //         if ($this->client->isAccessTokenExpired()) {
    //             $refreshToken = User::where('google_token', $accessToken)->value('google_refresh_token'); // Assuming it's stored in the user's record

    //             if ($refreshToken) {
    //                 $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
    //             } else {
    //                 return response()->json(['error' => 'Refresh token not found'], 401);
    //             }
    //         }

    //         // Initialize Google Classroom service
    //         $service = new Classroom($this->client);
    //         $courses = $service->courses->listCourses();

    //         return response()->json([
    //             'success' => true,
    //             'courses' => $courses->getCourses(),
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function listCourses(Request $request)
    {
        try {
            $accessToken = $request->bearerToken();

            if (!$accessToken) {
                return response()->json(['error' => 'Google token not found'], 400);
            }

            // Set the access token for the Google client
            $this->client->setAccessToken($accessToken);

            // Check if the access token is expired
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = User::where('google_token', $accessToken)->value('google_refresh_token'); // Assuming it's stored in the user's record

                if ($refreshToken) {
                    $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                } else {
                    return response()->json(['error' => 'Refresh token not found'], 401);
                }
            }

            // Initialize the Google Classroom service
            $service = new \Google\Service\Classroom($this->client);
            $courses = $service->courses->listCourses()->getCourses();

            if (empty($courses)) {
                return response()->json(['success' => true, 'courses' => []], 200);
            }

            // Fetch detailed course and student data
            $courseData = [];
            foreach ($courses as $course) {
                $students = null; // Default to null if no students are found
                try {
                    $studentList = $service->courses_students->listCoursesStudents($course->getId());
                    $students = [];

                    // Extract student names and email addresses
                    foreach ($studentList->getStudents() as $student) {
                        $students[] = [
                            'name' => $student->getProfile()->getName()->getFullName(),
                            'email' => $student->getProfile()->getEmailAddress(),
                        ];
                    }
                } catch (\Google\Service\Exception $e) {
                    if ($e->getCode() == 404) {
                        $students = null;
                    } else {
                        throw $e; // Rethrow other exceptions
                    }
                }

                // Include detailed course data
                $courseData[] = [
                    'id' => $course->getId(),
                    'name' => $course->getName(),
                    'section' => $course->getSection(),
                    'description' => $course->getDescription(),
                    'descriptionHeading' => $course->getDescriptionHeading(),
                    'room' => $course->getRoom(),
                    'ownerId' => $course->getOwnerId(),
                    'alternateLink' => $course->getAlternateLink(),
                    'calendarId' => $course->getCalendarId(),
                    'courseGroupEmail' => $course->getCourseGroupEmail(),
                    'teacherGroupEmail' => $course->getTeacherGroupEmail(),
                    'courseState' => $course->getCourseState(),
                    'creationTime' => $course->getCreationTime(),
                    'updateTime' => $course->getUpdateTime(),
                    'enrollmentCode' => $course->getEnrollmentCode(),
                    'guardiansEnabled' => $course->getGuardiansEnabled(),
                    'teacherFolder' => $course->getTeacherFolder()
                        ? [
                            'alternateLink' => $course->getTeacherFolder()->getAlternateLink(),
                            'id' => $course->getTeacherFolder()->getId(),
                            'title' => $course->getTeacherFolder()->getTitle(),
                        ]
                        : null,
                    'gradebookSettings' => $course->getGradebookSettings()
                        ? [
                            'calculationType' => $course->getGradebookSettings()->getCalculationType(),
                            'displaySetting' => $course->getGradebookSettings()->getDisplaySetting(),
                        ]
                        : null,
                    'students' => $students,
                ];
            }

            return response()->json(['success' => true, 'courses' => $courseData], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // Create a course
    public function createCourse(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
        ]);

        $teacher = User::find($validated['owner_id']);

        if ($teacher->role_id !== 3) { // Ensure the user is a Teacher
            return response()->json(['success' => false, 'error' => 'Invalid teacher role'], 403);
        }

        $this->client->setAccessToken($teacher->google_token);

        $this->classroomService = new Classroom($this->client);

        $course = new \Google\Service\Classroom\Course([
            'name' => $validated['name'],
            'section' => $validated['section'],
            'room' => $validated['room'],
            'descriptionHeading' => $validated['name'],
            'description' => $validated['description'],
            'ownerId' => $teacher->email,
        ]);

        $createdCourse = $this->classroomService->courses->create($course);

        GoogleCourse::create([
            'course_id' => $createdCourse->id,
            'name' => $validated['name'],
            'section' => $validated['section'],
            'room' => $validated['room'],
            'description' => $validated['description'],
            'owner_id' => $teacher->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Course added successfully', 'course' => $createdCourse], 201);
    }

    // get the list of students
    public function listStudents(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Google token not found'], 400);
        }

        try {
            // Set up the Google Client
            $this->client = new \Google\Client();
            $this->client->setAccessToken($accessToken);

            // Check if the access token is expired and refresh if necessary
            if ($this->client->isAccessTokenExpired()) {
                $refreshToken = User::where('google_token', $accessToken)->value('google_refresh_token');

                if ($refreshToken) {
                    $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                } else {
                    return response()->json(['error' => 'Refresh token not found'], 401);
                }
            }

            // Initialize Classroom Service
            // $this->classroomService = new \Google\Service\Classroom($this->client);

            // Fetch all courses for the authenticated user
            // $courses = $this->classroomService->courses->listCourses()->getCourses();
            $students = User::where('google_classroom_role', 'student')
                ->whereNotNull('google_id')
                ->orderBy('id', 'DESC')
                ->get();
            // if ($courses) {
            //     foreach ($courses as $course) {
            //         // Fetch students for each course
            //         $studentsResponse = $this->classroomService->courses_students->listCoursesStudents($course->getId());

            //         if ($studentsResponse->getStudents()) {
            //             foreach ($studentsResponse->getStudents() as $student) {
            //                 $profile = $student->getProfile();
            //                 $students[] = [
            //                     'id' => $student->getUserId(),
            //                     'name' => $profile->getName()->getFullName(),
            //                     'email' => $profile->getEmailAddress() ?: 'Email not available', // Handle missing emails
            //                     'course_id' => $course->getId(),
            //                     'course_name' => $course->getName(),
            //                 ];
            //             }
            //         }
            //     }
            // }

            // Remove duplicates based on 'id'
            // $uniqueStudents = array_values(array_reduce($students, function ($carry, $student) {
            //     $carry[$student['id']] = $student; // Use student ID as the key
            //     return $carry;
            // }, []));

            return response()->json([
                'success' => true,
                'students' => $students,
            ], 200);
        } catch (\Google\Service\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Add a student to a course
    public function addStudent(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        // Validate the request to ensure it contains a single user ID
        $validated = $request->validate([
            'course_id' => 'required|string',
            'user_id' => 'required|string', // Expecting a single user ID
        ]);

        // Debugging check
        if (!is_string($validated['user_id'])) {
            return response()->json(['success' => false, 'error' => 'user_id must be a string'], 400);
        }

        // Set the Google Client with the provided access token
        $this->client->setAccessToken($accessToken);

        // Check if token is expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = User::where('google_token', $accessToken)->value('google_refresh_token');

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['error' => 'Refresh token not found'], 401);
            }
        }

        // Initialize the Google Classroom Service
        $this->classroomService = new \Google\Service\Classroom($this->client);

        $responses = [];
        $errors = [];

        try {
            // Prepare the invitation for a single student
            $invitation = new \Google\Service\Classroom\Invitation([
                'courseId' => $validated['course_id'],
                'role' => 'STUDENT',
                'userId' => $validated['user_id'], // Use the single user_id
            ]);

            // Send the invitation
            $result = $this->classroomService->invitations->create($invitation);

            $responses[] = [
                'user_google_id' => $validated['user_id'],
                'message' => 'Invitation sent successfully',
                'invitation' => $result,
            ];
        } catch (\Google\Service\Exception $e) {
            // Handle specific errors such as entity already existing
            $errors[] = [
                'user_google_id' => $validated['user_id'],
                'message' => 'Requested entity already exists',
            ];
        } catch (\Exception $e) {
            // General errors
            $errors[] = [
                'user_google_id' => $validated['user_id'],
                'message' => 'The invited user already has the course role of a student',
            ];
        }

        // Return the response
        return response()->json([
            'success' => empty($errors),
            'message' => !empty($responses) ? $responses[0]['message'] : $errors[0]['message'],
            'responses' => $responses,
            'errors' => $errors,
        ], 200);
    }

    // Add a student to a course with multiple userIds
    // public function addStudents(Request $request)
    // {
    //     $accessToken = $request->bearerToken();

    //     if (!$accessToken) {
    //         return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
    //     }

    //     $validated = $request->validate([
    //         'course_id' => 'required|string',
    //         'user_ids' => 'required|array',
    //         'user_ids.*' => 'string',
    //     ]);


    //     // Debugging check
    //     if (!is_array($validated['user_ids'])) {
    //         return response()->json(['success' => false, 'error' => 'user_ids must be an array'], 400);
    //     }

    //     $this->client->setAccessToken($accessToken);

    //     // Check if token is expired
    //     if ($this->client->isAccessTokenExpired()) {
    //         $refreshToken = User::where('google_token', $accessToken)->value('google_refresh_token');

    //         if ($refreshToken) {
    //             $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
    //         } else {
    //             return response()->json(['error' => 'Refresh token not found'], 401);
    //         }
    //     }

    //     $this->classroomService = new \Google\Service\Classroom($this->client);

    //     $responses = [];
    //     $errors = [];

    //     foreach ($validated['user_ids'] as $studentGoogleId) {
    //         try {
    //             $invitation = new \Google\Service\Classroom\Invitation([
    //                 'courseId' => $validated['course_id'],
    //                 'role' => 'STUDENT',
    //                 'userId' => $studentGoogleId,
    //             ]);

    //             $result = $this->classroomService->invitations->create($invitation);

    //             $responses[] = [
    //                 'user_google_id' => $studentGoogleId,
    //                 'message' => 'Invitation sent successfully',
    //                 'invitation' => $result,
    //             ];
    //         } catch (\Google\Service\Exception $e) {
    //             $errors[] = [
    //                 'user_google_id' => $studentGoogleId,
    //                 'message' => 'Requested entity already exists',
    //                 // 'error' => $e->getMessage(),
    //             ];
    //         } catch (\Exception $e) {
    //             $errors[] = [
    //                 'user_google_id' => $studentGoogleId,
    //                 'message' => 'The invited user already has the course role of a student',
    //                 // 'error' => $e->getMessage(),
    //             ];
    //         }
    //     }

    //     return response()->json([
    //         'success' => empty($errors),
    //         'responses' => $responses,
    //         'errors' => $errors,
    //     ], 200);
    // }

    // get the list of assignments
    public function listAssignments(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:google_courses,course_id',
        ]);

        $course = GoogleCourse::where('course_id', $validated['course_id'])->first();
        $teacher = User::find($course->owner_id);

        $this->client->setAccessToken($teacher->google_token);

        // Refresh token if needed
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $teacher->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['error' => 'Refresh token not found'], 401);
            }
        }

        $this->classroomService = new \Google\Service\Classroom($this->client);

        try {
            // Get the list of assignments (CourseWork) for the course
            $courseWorkList = $this->classroomService->courses_courseWork->listCoursesCourseWork($validated['course_id']);

            $assignments = [];
            if ($courseWorkList->getCourseWork()) {
                foreach ($courseWorkList->getCourseWork() as $courseWork) {
                    $assignments[] = [
                        'id' => $courseWork->getId(),
                        'title' => $courseWork->getTitle(),
                        'description' => $courseWork->getDescription(),
                        'workType' => $courseWork->getWorkType(),
                        'state' => $courseWork->getState(),
                        'dueDate' => $courseWork->getDueDate(),
                        'dueTime' => $courseWork->getDueTime(),
                        'creationTime' => $courseWork->getCreationTime(),
                        'updateTime' => $courseWork->getUpdateTime(),
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'assignments' => $assignments,
            ], 200);
        } catch (\Google\Service\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Create an assignment
    public function createAssignment(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:google_courses,course_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date', // Date in 'Y-m-d' format
            'due_time' => 'nullable|date_format:H:i', // Time in 'HH:mm' format (24-hour clock)
        ]);

        $course = GoogleCourse::where('course_id', $validated['course_id'])->first();
        $teacher = User::find($course->owner_id);

        $this->client->setAccessToken($teacher->google_token);

        // Check if the token has expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $teacher->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['success' => false, 'error' => 'Refresh token not found'], 401);
            }
        }

        $this->classroomService = new Classroom($this->client);

        $courseworkData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'workType' => 'ASSIGNMENT', // Required field for Google Classroom
            'state' => 'PUBLISHED', // Optional: 'PUBLISHED' or 'DRAFT'
        ];

        // Add dueDate and dueTime if provided
        if (!empty($validated['due_date'])) {
            $dueDate = explode('-', $validated['due_date']);
            $courseworkData['dueDate'] = [
                'year' => (int) $dueDate[0],
                'month' => (int) $dueDate[1],
                'day' => (int) $dueDate[2],
            ];
        }

        if (!empty($validated['due_time'])) {
            $dueTime = explode(':', $validated['due_time']);
            $courseworkData['dueTime'] = [
                'hours' => (int) $dueTime[0],
                'minutes' => (int) $dueTime[1],
            ];
        }

        $coursework = new \Google\Service\Classroom\CourseWork($courseworkData);

        try {
            $createdAssignment = $this->classroomService->courses_courseWork->create($validated['course_id'], $coursework);

            GoogleAssignment::create([
                'assignment_id' => $createdAssignment->id,
                'course_id' => $validated['course_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'due_date' => $validated['due_date'],
                'due_time' => $validated['due_time'],
            ]);

            return response()->json(['success' => true, 'message' => 'Assignment created successfully', 'assignment' => $createdAssignment], 201);
        } catch (\Google\Service\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // this method is used for teacher dashboard
    public function teacherDashboard(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
        }

        $teacher = User::where('google_token', $accessToken)->first();

        if ($teacher->google_classroom_role !== "teacher") {
            return response()->json(['success' => false, 'message' => 'Access denied. Please log in with a teacher account.'], 400);
        }

        try {
            // Fetch all courses owned by the teacher
            $courses = GoogleCourse::where('owner_id', $teacher->id)->get();

            $totalAssignments = $courses->map(function ($course) {
                return $course->assignments->count();
            })->sum();

            // $studentCount = $courses->map(function ($course) {
            //     return $course->participants->pluck('user_id')->unique('user_id')->count();
            // })->sum();

            $studentCount = User::where('google_classroom_role', 'student')
                ->whereNotNull('google_id')
                ->count();
            
            $activeAssignments = $courses->map(function ($course) {
                return $course->assignments->where('due_date', '>', now())->count();
            })->sum();

            // $service = new Classroom($this->client);
            // $totalCourses = $service->courses->listCourses();
            $totalCourses = $courses->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_assignments' => $totalAssignments,
                    'students' => $studentCount,
                    'active_assignments' => $activeAssignments,
                    'total_courses' => $totalCourses,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // get invitation list sent by teacher
    public function getStudentInvitations(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        // Validate if the Google token is valid for a student
        $student = User::where('google_token', $accessToken)->first();

        if (!$student) {
            return response()->json(['success' => false, 'error' => 'Student not found or invalid token'], 404);
        }

        $this->client->setAccessToken($accessToken);

        // Check if the token has expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $student->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['success' => false, 'error' => 'Refresh token not found'], 401);
            }
        }

        // Initialize Google Classroom Service
        $this->classroomService = new \Google\Service\Classroom($this->client);

        // Validate request parameters
        $validated = $request->validate([
            'user_google_id' => 'nullable|string', // Either user_google_id or course_id should be provided
            'course_id' => 'nullable|string',
        ]);

        if (empty($validated['user_google_id']) && empty($validated['course_id'])) {
            return response()->json(['success' => false, 'error' => 'Either user_google_id or course_id must be provided'], 400);
        }

        try {
            // Prepare the query parameters
            $queryParams = [];

            if (!empty($validated['user_google_id'])) {
                $queryParams['userId'] = $validated['user_google_id'];
            } else {
                $queryParams['userId'] = 'me'; // Default to the current authenticated user
            }

            if (!empty($validated['course_id'])) {
                $queryParams['courseId'] = $validated['course_id'];
            }

            // Fetch invitations based on the parameters
            $invitationsList = $this->classroomService->invitations->listInvitations($queryParams);
            $invitations = $invitationsList->getInvitations();

            // Prepare response data
            $responseData = [];

            if ($invitations) {
                foreach ($invitations as $invitation) {
                    try {
                        // Fetch course details
                        $course = $this->classroomService->courses->get($invitation->getCourseId());

                        // Fetch inviter details
                        $inviterName = null;
                        if ($course->getOwnerId()) {
                            $inviterProfile = $this->classroomService->userProfiles->get($course->getOwnerId());
                            $inviterName = $inviterProfile->getName()->getFullName();
                        }

                        $responseData[] = [
                            'invitation_id' => $invitation->getId(),
                            'course_id' => $invitation->getCourseId(),
                            'course_name' => $course->getName(),
                            'role' => $invitation->getRole(),
                            'invited_by_name' => $inviterName,
                        ];
                    } catch (\Google\Service\Exception $e) {
                        // If course details or inviter details are not found, skip
                        continue;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'invitations' => $responseData,
            ], 200);
        } catch (\Google\Service\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching invitations',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    // accept the invitations send by teacher
    public function acceptInvitation(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        // Validate if the Google token is valid for a student
        $student = User::where('google_token', $accessToken)->first();

        if (!$student) {
            return response()->json(['success' => false, 'error' => 'Student not found or invalid token'], 404);
        }
        $this->client->setAccessToken($accessToken);

        // Check if the token has expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $student->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['success' => false, 'error' => 'Refresh token not found'], 401);
            }
        }

        // Initialize Google Classroom Service
        $this->classroomService = new \Google\Service\Classroom($this->client);

        // Validate request parameters
        $validated = $request->validate([
            'invitation_id' => 'required|string', // Invitation ID to accept
        ]);

        try {
            // Accept the invitation
            $this->classroomService->invitations->accept($validated['invitation_id']);

            return response()->json([
                'success' => true,
                'message' => 'Invitation accepted successfully',
            ], 200);
        } catch (\Google\Service\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error accepting invitation',
                // 'error' => 'Error accepting invitation: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }

    // reject the invitations send by teacher
    public function deleteInvitation(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'error' => 'Google token not found'], 400);
        }

        // Validate if the Google token is valid for a student
        $student = User::where('google_token', $accessToken)->first();

        if (!$student) {
            return response()->json(['success' => false, 'error' => 'Student not found or invalid token'], 404);
        }

        $this->client->setAccessToken($accessToken);

        // Check if the token has expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $student->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['success' => false, 'error' => 'Refresh token not found'], 401);
            }
        }

        // Initialize Google Classroom Service
        $this->classroomService = new \Google\Service\Classroom($this->client);

        // Validate request parameters
        $validated = $request->validate([
            'invitation_id' => 'required|string', // Invitation ID to delete
        ]);

        try {
            // decline the invitation
            $this->classroomService->invitations->delete($validated['invitation_id']);

            return response()->json([
                'success' => true,
                'message' => 'Invitation rejected successfully',
            ], 200);
        } catch (\Google\Service\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting invitation',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred',
            ], 500);
        }
    }

    // this method is used to get the courses of student
    public function getStudentCourses(Request $request)
    {
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['success' => false, 'message' => 'Google token not found'], 400);
        }

        // Validate if the Google token is valid for a student
        $student = User::where('google_token', $accessToken)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found or invalid token'], 404);
        }

        $this->client->setAccessToken($accessToken);

        // Check if the token has expired
        if ($this->client->isAccessTokenExpired()) {
            $refreshToken = $student->google_refresh_token;

            if ($refreshToken) {
                $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
            } else {
                return response()->json(['success' => false, 'error' => 'Refresh token not found'], 401);
            }
        }

        // Initialize the Google Classroom service
        $classroomService = new \Google\Service\Classroom($this->client);

        try {
            // Fetch the courses the student is enrolled in
            $courses = $classroomService->courses->listCourses()->getCourses();

            if (empty($courses)) {
                return response()->json(['success' => false, 'message' => 'No courses available.'], 404);
            }

            // Extract course names and links
            $courseLinks = [];
            foreach ($courses as $course) {
                $inviterName = null;

                try {
                    // fetch inviter's name
                    if ($course->getOwnerId()) {
                        $inviterProfile = $classroomService->userProfiles->get($course->getOwnerId());
                        $inviterName = $inviterProfile->getName()->getFullName();
                    }
                } catch (\Exception $e) {
                    $inviterName = 'Unknown';
                }

                $courseLinks[] = [
                    'name' => $course->getName(),
                    'link' => $course->getAlternateLink(),
                    'created_by' => $inviterName,
                ];
            }

            return response()->json(['success' => true, 'courses' => $courseLinks], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching courses: ' . $e->getMessage()], 500);
        }
    }
}
