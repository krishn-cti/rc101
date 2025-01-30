<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CMS\EventCoverageController;
use App\Http\Controllers\Admin\CMS\LeaderController;
use App\Http\Controllers\Admin\CMS\Lesson3dModelingController;
use App\Http\Controllers\Admin\CMS\Lesson3dPrintingController;
use App\Http\Controllers\Admin\CMS\LessonBatteriesController;
use App\Http\Controllers\Admin\CMS\LessonBescsController;
use App\Http\Controllers\Admin\CMS\LessonBrushedBrushlessController;
use App\Http\Controllers\Admin\CMS\LessonElectricalEngineeringController;
use App\Http\Controllers\Admin\CMS\LessonFusionController;
use App\Http\Controllers\Admin\CMS\LessonGearRatiosController;
use App\Http\Controllers\Admin\CMS\LessonMaterialScienceController;
use App\Http\Controllers\Admin\CMS\LessonPcbsController;
use App\Http\Controllers\Admin\CMS\LessonPhysicsGeometryController;
use App\Http\Controllers\Admin\CMS\LessonReceiversController;
use App\Http\Controllers\Admin\CMS\LessonSlicingController;
use App\Http\Controllers\Admin\CMS\LessonSolderingController;
use App\Http\Controllers\Admin\CMS\LessonThinkercadController;
use App\Http\Controllers\Admin\CMS\LessonWeaponPhysicsController;
use App\Http\Controllers\Admin\CMS\PartnerController;
use App\Http\Controllers\Admin\CMS\PresentationController;
use App\Http\Controllers\Admin\CMS\ServiceController;
use App\Http\Controllers\Admin\CMS\SparcRuleController;
use App\Http\Controllers\Admin\CMS\ToolsTradeController;
use App\Http\Controllers\Admin\CMS\TournamentController;
use App\Http\Controllers\Admin\CMS\WeightAntweightController;
use App\Http\Controllers\Admin\CMS\WeightBeetleweightController;
use App\Http\Controllers\Admin\CMS\WeightClassController;
use App\Http\Controllers\Admin\CMS\WeightFairyweightController;
use App\Http\Controllers\Admin\CMS\WeightFeatherweightController;
use App\Http\Controllers\Admin\CMS\WeightHobbyweightController;
use App\Http\Controllers\Admin\CMS\WeightPlasticAntweightController;
use App\Http\Controllers\Admin\CMS\WeightSportsmanController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ContentManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

// Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::controller(StripePaymentController::class)->group(function () {
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});

// ============== Admin Routes =========== ----code by krishn starts----
Route::post('/admin-login', [AdminController::class, 'login'])->name('admin-login');
Route::post('/admin-login-submit',  [AdminController::class, 'adminLoginSubmit']);
Route::get('/admin-logout', [AdminController::class, 'logout']);

Route::group(['middleware' => ['admin']], function () {
    Route::controller(AdminController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');
        Route::get('/get-profile', 'getProfile')->name('get-profile');
        Route::get('/edit-profile', 'editProfile')->name('edit-profile');
        Route::post('/update-profile', 'updateProfile')->name('update-profile');
        Route::get('/change-password', 'changePassword')->name('change-password');
        Route::post('/update-password', 'updatePassword')->name('update-password');
    });

    // route for products
    Route::controller(ProductController::class)->group(function () {
        Route::get('/list-product', 'index');
        Route::get('/add-product', 'create');
        Route::get('/edit-product/{id}', 'edit');
        Route::post('/save-product', 'store');
        Route::post('/update-product', 'update');
        Route::post('/delete-product', 'destroy');
        Route::get('/subcategories', 'getSubCategory');
    });

    // route for product category
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/list-category', 'index');
        Route::get('/add-category', 'create');
        Route::get('/edit-category/{id}', 'edit');
        Route::post('/save-category', 'store');
        Route::post('/update-category', 'update');
        Route::post('/delete-category', 'destroy');
    });

    // route for product sub category
    Route::controller(SubCategoryController::class)->group(function () {
        Route::get('/list-sub-category', 'index');
        Route::get('/add-sub-category', 'create');
        Route::get('/edit-sub-category/{id}', 'edit');
        Route::post('/save-sub-category', 'store');
        Route::post('/update-sub-category', 'update');
        Route::post('/delete-sub-category', 'destroy');
    });

    // route for user
    Route::controller(UserController::class)->group(function () {
        Route::get('/list-user', 'index');
        Route::get('/add-user', 'create');
        Route::get('/edit-user/{id}', 'edit');
        Route::post('/save-user', 'store');
        Route::post('/update-user', 'update');
        Route::post('/delete-user', 'destroy');
    });

    // route for Content Management
    Route::prefix('cms')->group(function () {
        // route for tournaments
        Route::controller(TournamentController::class)->group(function () {
            Route::get('/tournament-list', 'index');
            Route::get('/tournament-add', 'create');
            Route::post('/tournament-save', 'store');
            Route::get('/tournament-edit/{id}', 'edit');
            Route::post('/tournament-update', 'update');
            Route::post('/tournament-delete', 'destroy');
        });

        // route for presentations
        Route::controller(PresentationController::class)->group(function () {
            Route::get('/presentation-list', 'index');
            Route::get('/presentation-add', 'create');
            Route::post('/presentation-save', 'store');
            Route::get('/presentation-edit/{id}', 'edit');
            Route::post('/presentation-update', 'update');
            Route::post('/presentation-delete', 'destroy');
        });

        // route for weight classes/restrictions
        Route::controller(WeightClassController::class)->group(function () {
            Route::get('/weight-class-list', 'index');
            Route::get('/weight-class-add', 'create');
            Route::post('/weight-class-save', 'store');
            Route::get('/weight-class-edit/{id}', 'edit');
            Route::post('/weight-class-update', 'update');
            Route::post('/weight-class-delete', 'destroy');
        });

        // route for services
        Route::controller(ServiceController::class)->group(function () {
            Route::get('/service-list', 'index');
            Route::get('/service-add', 'create');
            Route::post('/service-save', 'store');
            Route::get('/service-edit/{id}', 'edit');
            Route::post('/service-update', 'update');
            Route::post('/service-delete', 'destroy');
        });

        // route for partners(companies)
        Route::controller(PartnerController::class)->group(function () {
            Route::get('/partner-list', 'index');
            Route::get('/partner-add', 'create');
            Route::post('/partner-save', 'store');
            Route::get('/partner-edit/{id}', 'edit');
            Route::post('/partner-update', 'update');
            Route::post('/partner-delete', 'destroy');
        });

        // route for leaders
        // Route::controller(LeaderController::class)->group(function () {
        //     Route::get('/leader-list', 'index');
        //     Route::get('/leader-add', 'create');
        //     Route::post('/leader-save', 'store');
        //     Route::get('/leader-edit/{id}', 'edit');
        //     Route::post('/leader-update', 'update');
        //     Route::post('/leader-delete', 'destroy');
        // });

        // route for SPARC Rules
        Route::controller(SparcRuleController::class)->group(function () {
            Route::get('/sparc-rule-list', 'index');
            Route::get('/sparc-rule-add', 'create');
            Route::post('/sparc-rule-save', 'store');
            Route::get('/sparc-rule-edit/{id}', 'edit');
            Route::post('/sparc-rule-update', 'update');
            Route::post('/sparc-rule-delete', 'destroy');
        });

        // route for Event Coverage/Results
        Route::controller(EventCoverageController::class)->group(function () {
            Route::get('/event-coverage-list', 'index');
            Route::get('/event-coverage-add', 'create');
            Route::post('/event-coverage-save', 'store');
            Route::get('/event-coverage-edit/{id}', 'edit');
            Route::post('/event-coverage-update', 'update');
            Route::post('/event-coverage-delete', 'destroy');
        });

        // route for Tools of the Trade
        Route::controller(ToolsTradeController::class)->group(function () {
            Route::get('/tools-trade-list', 'index');
            Route::get('/tools-trade-add', 'create');
            Route::post('/tools-trade-save', 'store');
            Route::get('/tools-trade-edit/{id}', 'edit');
            Route::post('/tools-trade-update', 'update');
            Route::post('/tools-trade-delete', 'destroy');
        });

        // Routes for all lessons
        Route::prefix('lessons')->group(function () {

            // Routes for CMS Lesson 3D Modeling
            Route::controller(Lesson3dModelingController::class)->group(function () {
                Route::get('/3d-modeling-list', 'index');
                Route::get('/3d-modeling-add', 'create');
                Route::post('/3d-modeling-save', 'store');
                Route::get('/3d-modeling-edit/{id}', 'edit');
                Route::post('/3d-modeling-update', 'update');
                Route::post('/3d-modeling-delete', 'destroy');
            });

            // Routes for CMS Lesson 3D Printing
            Route::controller(Lesson3dPrintingController::class)->group(function () {
                Route::get('/3d-printing-list', 'index');
                Route::get('/3d-printing-add', 'create');
                Route::post('/3d-printing-save', 'store');
                Route::get('/3d-printing-edit/{id}', 'edit');
                Route::post('/3d-printing-update', 'update');
                Route::post('/3d-printing-delete', 'destroy');
            });

            // Routes for CMS Lesson Batteries
            Route::controller(LessonBatteriesController::class)->group(function () {
                Route::get('/batteries-list', 'index');
                Route::get('/batteries-add', 'create');
                Route::post('/batteries-save', 'store');
                Route::get('/batteries-edit/{id}', 'edit');
                Route::post('/batteries-update', 'update');
                Route::post('/batteries-delete', 'destroy');
            });

            // Routes for CMS Lesson BESCs
            Route::controller(LessonBescsController::class)->group(function () {
                Route::get('/bescs-list', 'index');
                Route::get('/bescs-add', 'create');
                Route::post('/bescs-save', 'store');
                Route::get('/bescs-edit/{id}', 'edit');
                Route::post('/bescs-update', 'update');
                Route::post('/bescs-delete', 'destroy');
            });

            // Routes for CMS Lesson Brushed and Brushless
            Route::controller(LessonBrushedBrushlessController::class)->group(function () {
                Route::get('/brushed-brushless-list', 'index');
                Route::get('/brushed-brushless-add', 'create');
                Route::post('/brushed-brushless-save', 'store');
                Route::get('/brushed-brushless-edit/{id}', 'edit');
                Route::post('/brushed-brushless-update', 'update');
                Route::post('/brushed-brushless-delete', 'destroy');
            });

            // Routes for CMS Lesson Electrical Engineering
            Route::controller(LessonElectricalEngineeringController::class)->group(function () {
                Route::get('/electrical-engineering-list', 'index');
                Route::get('/electrical-engineering-add', 'create');
                Route::post('/electrical-engineering-save', 'store');
                Route::get('/electrical-engineering-edit/{id}', 'edit');
                Route::post('/electrical-engineering-update', 'update');
                Route::post('/electrical-engineering-delete', 'destroy');
            });

            // Routes for CMS Lesson Fusion
            Route::controller(LessonFusionController::class)->group(function () {
                Route::get('/fusion-list', 'index');
                Route::get('/fusion-add', 'create');
                Route::post('/fusion-save', 'store');
                Route::get('/fusion-edit/{id}', 'edit');
                Route::post('/fusion-update', 'update');
                Route::post('/fusion-delete', 'destroy');
            });

            // Routes for CMS Lesson Gear Ratios
            Route::controller(LessonGearRatiosController::class)->group(function () {
                Route::get('/gear-ratios-list', 'index');
                Route::get('/gear-ratios-add', 'create');
                Route::post('/gear-ratios-save', 'store');
                Route::get('/gear-ratios-edit/{id}', 'edit');
                Route::post('/gear-ratios-update', 'update');
                Route::post('/gear-ratios-delete', 'destroy');
            });

            // Routes for CMS Lesson Material Science
            Route::controller(LessonMaterialScienceController::class)->group(function () {
                Route::get('/material-science-list', 'index');
                Route::get('/material-science-add', 'create');
                Route::post('/material-science-save', 'store');
                Route::get('/material-science-edit/{id}', 'edit');
                Route::post('/material-science-update', 'update');
                Route::post('/material-science-delete', 'destroy');
            });

            // Routes for CMS Lesson PCBs
            Route::controller(LessonPcbsController::class)->group(function () {
                Route::get('/pcbs-list', 'index');
                Route::get('/pcbs-add', 'create');
                Route::post('/pcbs-save', 'store');
                Route::get('/pcbs-edit/{id}', 'edit');
                Route::post('/pcbs-update', 'update');
                Route::post('/pcbs-delete', 'destroy');
            });

            // Routes for CMS Lesson Physics and Geometry
            Route::controller(LessonPhysicsGeometryController::class)->group(function () {
                Route::get('/physics-geometry-list', 'index');
                Route::get('/physics-geometry-add', 'create');
                Route::post('/physics-geometry-save', 'store');
                Route::get('/physics-geometry-edit/{id}', 'edit');
                Route::post('/physics-geometry-update', 'update');
                Route::post('/physics-geometry-delete', 'destroy');
            });

            // Routes for CMS Lesson Receivers
            Route::controller(LessonReceiversController::class)->group(function () {
                Route::get('/receivers-list', 'index');
                Route::get('/receivers-add', 'create');
                Route::post('/receivers-save', 'store');
                Route::get('/receivers-edit/{id}', 'edit');
                Route::post('/receivers-update', 'update');
                Route::post('/receivers-delete', 'destroy');
            });

            // Routes for CMS Lesson Slicing
            Route::controller(LessonSlicingController::class)->group(function () {
                Route::get('/slicing-list', 'index');
                Route::get('/slicing-add', 'create');
                Route::post('/slicing-save', 'store');
                Route::get('/slicing-edit/{id}', 'edit');
                Route::post('/slicing-update', 'update');
                Route::post('/slicing-delete', 'destroy');
            });

            // Routes for CMS Lesson Soldering
            Route::controller(LessonSolderingController::class)->group(function () {
                Route::get('/soldering-list', 'index');
                Route::get('/soldering-add', 'create');
                Route::post('/soldering-save', 'store');
                Route::get('/soldering-edit/{id}', 'edit');
                Route::post('/soldering-update', 'update');
                Route::post('/soldering-delete', 'destroy');
            });

            // Routes for CMS Lesson Thinkercad
            Route::controller(LessonThinkercadController::class)->group(function () {
                Route::get('/thinkercad-list', 'index');
                Route::get('/thinkercad-add', 'create');
                Route::post('/thinkercad-save', 'store');
                Route::get('/thinkercad-edit/{id}', 'edit');
                Route::post('/thinkercad-update', 'update');
                Route::post('/thinkercad-delete', 'destroy');
            });

            // Routes for CMS Lesson Weapon Physics
            Route::controller(LessonWeaponPhysicsController::class)->group(function () {
                Route::get('/weapon-physics-list', 'index');
                Route::get('/weapon-physics-add', 'create');
                Route::post('/weapon-physics-save', 'store');
                Route::get('/weapon-physics-edit/{id}', 'edit');
                Route::post('/weapon-physics-update', 'update');
                Route::post('/weapon-physics-delete', 'destroy');
            });
        });

        // Routes for all weight-classes
        Route::prefix('weight-classes')->group(function () {

            // Routes for CMS Weight Antweight
            Route::controller(WeightAntweightController::class)->group(function () {
                Route::get('/antweight-list', 'index');
                Route::get('/antweight-add', 'create');
                Route::post('/antweight-save', 'store');
                Route::get('/antweight-edit/{id}', 'edit');
                Route::post('/antweight-update', 'update');
                Route::post('/antweight-delete', 'destroy');
            });

            // Routes for CMS Weight Beetleweight
            Route::controller(WeightBeetleweightController::class)->group(function () {
                Route::get('/beetleweight-list', 'index');
                Route::get('/beetleweight-add', 'create');
                Route::post('/beetleweight-save', 'store');
                Route::get('/beetleweight-edit/{id}', 'edit');
                Route::post('/beetleweight-update', 'update');
                Route::post('/beetleweight-delete', 'destroy');
            });

            // Routes for CMS Weight Fairyweight
            Route::controller(WeightFairyweightController::class)->group(function () {
                Route::get('/fairyweight-list', 'index');
                Route::get('/fairyweight-add', 'create');
                Route::post('/fairyweight-save', 'store');
                Route::get('/fairyweight-edit/{id}', 'edit');
                Route::post('/fairyweight-update', 'update');
                Route::post('/fairyweight-delete', 'destroy');
            });

            // Routes for CMS Weight Featherweight
            Route::controller(WeightFeatherweightController::class)->group(function () {
                Route::get('/featherweight-list', 'index');
                Route::get('/featherweight-add', 'create');
                Route::post('/featherweight-save', 'store');
                Route::get('/featherweight-edit/{id}', 'edit');
                Route::post('/featherweight-update', 'update');
                Route::post('/featherweight-delete', 'destroy');
            });

            // Routes for CMS Weight Hobbyweight
            Route::controller(WeightHobbyweightController::class)->group(function () {
                Route::get('/hobbyweight-list', 'index');
                Route::get('/hobbyweight-add', 'create');
                Route::post('/hobbyweight-save', 'store');
                Route::get('/hobbyweight-edit/{id}', 'edit');
                Route::post('/hobbyweight-update', 'update');
                Route::post('/hobbyweight-delete', 'destroy');
            });

            // Routes for CMS Weight Plastic Antweight
            Route::controller(WeightPlasticAntweightController::class)->group(function () {
                Route::get('/plastic-antweight-list', 'index');
                Route::get('/plastic-antweight-add', 'create');
                Route::post('/plastic-antweight-save', 'store');
                Route::get('/plastic-antweight-edit/{id}', 'edit');
                Route::post('/plastic-antweight-update', 'update');
                Route::post('/plastic-antweight-delete', 'destroy');
            });

            // Routes for CMS Weight Sportsman
            Route::controller(WeightSportsmanController::class)->group(function () {
                Route::get('/sportsman-list', 'index');
                Route::get('/sportsman-add', 'create');
                Route::post('/sportsman-save', 'store');
                Route::get('/sportsman-edit/{id}', 'edit');
                Route::post('/sportsman-update', 'update');
                Route::post('/sportsman-delete', 'destroy');
            });
        });

        Route::controller(ContentManagementController::class)->group(function () {
            // route for home page
            Route::get('/home', 'editHome');
            Route::post('/update-home', 'updateHome');

            // route for about page
            Route::get('/about', 'editAbout');
            Route::post('/update-about', 'updateAbout');

            // route for league page
            Route::get('/league', 'editLeague');
            Route::post('/update-league', 'updateLeague');
            
            // route for glossary of terms
            Route::get('/glossary-term', 'editGlossaryTerm');
            Route::post('/update-glossary-term', 'updateGlossaryTerm');
            
            // route for privacy policy
            Route::get('/privacy-policy', 'editPrivacyPolicy');
            Route::post('/update-privacy-policy', 'updatePrivacyPolicy');
        });
    });

    Route::get('list-order', [OrderController::class, 'listOrder']);
});
