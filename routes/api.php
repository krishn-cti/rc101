<?php

use App\Http\Controllers\API\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ContentManagementController;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes of authtication
Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    // Route::post('reset-password', 'resetPassword')->name('password.update');

});

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Public routes of product
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{id}', 'show');
    Route::get('/get-related-products/{related_name}', 'getRelatedProduct');
});

// Protected routes of product, cart and logout
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout']);
    Route::post('/update-profile', [RegisterController::class, 'updateProfile']);
    Route::get('/get-my-profile', [RegisterController::class, 'getMyProfile']);
    Route::get('/get-my-addresses', [RegisterController::class, 'getMyAddresses']);
    Route::post('/remove-billing-address', [RegisterController::class, 'removeBillingAddress']);

    Route::controller(ProductController::class)->group(function () {
        Route::post('/products', 'store');
        Route::post('/products/{id}', 'update');
        Route::delete('/products/{id}', 'destroy');
        Route::post('/send-product-review', 'sendProductReview');
    });

    Route::controller(CartController::class)->group(function () {
        Route::get('get-cart-details', 'getCartDetails');
        Route::post('add-to-cart', 'addToCart');
        Route::post('update-cart', 'updateCart');
        Route::post('remove-from-cart', 'removeFromCart');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::post('place-order', 'placeOrder');
        Route::get('get-my-orders', 'getMyOrders');
        Route::get('create-order', 'createOrder');
        Route::post('complete-order', 'completeOrder');
    });
});

// public route for CMS
Route::controller(ContentManagementController::class)->group(function () {
    Route::get('get-all-service', 'getAllService');
    Route::get('get-service-details', 'getServiceDetails');
    Route::get('get-all-partner', 'getAllPartner');
    Route::get('get-all-leader', 'getAllLeader');
    Route::get('get-about-section', 'getAboutSection');
    Route::get('get-home-section', 'getHomeSection');
    Route::get('get-league-page', 'getLeaguePage');
    Route::get('get-all-tournament', 'getAllTournament');
    Route::get('get-all-presentation', 'getAllPresentation');
    Route::get('get-all-sparc-rule', 'getAllSparcRule');
    Route::get('get-all-weight-class-restriction', 'getAllWeightClassRestriction');
    Route::get('get-all-event-coverage', 'getAllEventCoverage');
    Route::get('get-all-tools-trade', 'getAllToolsTrade');
    Route::get('get-glossary-term', 'getGlossaryTerm');
    Route::get('get-privacy-policy', 'getPrivacyPolicy');
    
    // Routes for Weight clasess
    Route::get('get-all-lesson-3d-modeling', 'getAllLesson3dModeling');
    Route::get('get-all-lesson-3d-printing', 'getAllLesson3dPrinting');
    Route::get('get-all-lesson-batteries', 'getAllLessonBatteries');
    Route::get('get-all-lesson-bescs', 'getAllLessonBescs');
    Route::get('get-all-lesson-brushed-brushless', 'getAllLessonBrushedBrushless');
    Route::get('get-all-lesson-electrical-engineering', 'getAllLessonElectricalEngineering');
    Route::get('get-all-lesson-fusion', 'getAllLessonFusion');
    Route::get('get-all-lesson-gear-ratios', 'getAllLessonGearRatios');
    Route::get('get-all-lesson-material-science', 'getAllLessonMaterialScience');
    Route::get('get-all-lesson-pcbs', 'getAllLessonPcbs');
    Route::get('get-all-lesson-physics-geometry', 'getAllLessonPhysicsGeometry');
    Route::get('get-all-lesson-receivers', 'getAllLessonReceivers');
    Route::get('get-all-lesson-slicing', 'getAllLessonSlicing');
    Route::get('get-all-lesson-soldering', 'getAllLessonSoldering');
    Route::get('get-all-lesson-thinkercad', 'getAllLessonThinkercad');
    Route::get('get-all-lesson-weapon-physics', 'getAllLessonWeaponPhysics');
    
    // Routes for Weight clasess
    Route::get('get-all-weight-antweight', 'getAllAntweight');
    Route::get('get-all-weight-beetleweight', 'getAllBeetleweight');
    Route::get('get-all-weight-fairyweight', 'getAllFairyweight');
    Route::get('get-all-weight-featherweight', 'getAllFeatherweight');
    Route::get('get-all-weight-hobbyweight', 'getAllHobbyweight');
    Route::get('get-all-weight-plastic-antweight', 'getAllPlasticAntweight');
    Route::get('get-all-weight-sportsman', 'getAllSportsman');

    Route::post('contact-us', 'contactUs');
});
