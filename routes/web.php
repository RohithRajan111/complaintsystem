<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController; // Import the new controller
use App\Http\Controllers\DeptController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// --- PUBLIC ROUTES ---
Route::get('/', function () {
    return view('welcome');
})->name('welcome');



// Route::get('/', function () {
//     return view('data');
// })->name('data');

    // complaints-datatable
    Route::get('/data/complaints-datatable', [AdminController::class, 'complaintsDatatable'])->name('admin.complaints.datatable');
    Route::get('/data/complaints-datatable-e', [AdminController::class, 'complaintsDatatableEager'])->name('admin.complaints.datatable.e');


// Unified Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // A single logout route for everyone

// Student Registration
Route::get('/student_register', [StudentController::class, 'showregister'])->name('showstudent.register');
Route::post('/student_register', [StudentController::class, 'register'])->name('student.register');

// Department Registration (can only be accessed by an authenticated department user, likely an admin should create them)
// Route::get('/dept_register', [DeptController::class, 'register'])->name('showdept.register')->middleware('auth:depts');

// --- STUDENT ROUTES (Protected by 'auth' or 'auth:web' guard) ---
Route::middleware('auth:web')->group(function () {
    Route::get('/student_dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/make-complaint-form', [StudentController::class, 'ajaxMakeComplaintForm'])->name('student.ajax.complaint.form');
    Route::post('/student/make-complaint', [StudentController::class, 'submitComplaint'])->name('student.ajax.complaint.submit');
    Route::get('/student/my-complaints', [StudentController::class, 'ajaxMyComplaints'])->name('student.ajax.complaint.list');
    Route::post('/student/withdraw-complaint/{id}', [StudentController::class, 'withdrawComplaint'])->name('student.complaint.withdraw');
    Route::get('/student/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::post('/student/profile', [StudentController::class, 'updateProfile'])->name('student.profile.update');
});

// --- DEPARTMENT ROUTES (Protected by 'auth:depts' guard) ---
Route::middleware('auth:depts')->group(function () {
    Route::get('/dept_dashboard', [DeptController::class, 'dashboard'])->name('showdept.dashboard');

    Route::get('/dept/complaint/{id}', [DeptController::class, 'show'])->name('dept.complaint.show');

    Route::post('/dept/respond/{id}', [DeptController::class, 'respond'])->name('dept.respond');

    Route::get('/dept/profile', [DeptController::class, 'showProfile'])->name('dept.profile.show');
    Route::post('/dept/profile', [DeptController::class, 'updateProfile'])->name('dept.profile.update');
});

Route::middleware('auth:admins')->group(function () {
    Route::get('/admin_dashboard', [AdminController::class, 'dashboard'])->name('showadmin.dashboard');

    // AJAX routes for loading tab content
    Route::get('/admin_dashboard/ajax/complaints', [AdminController::class, 'ajaxComplaints'])->name('admin.complaints.ajax');
    Route::get('/admin_dashboard/ajax/logs', [AdminController::class, 'ajaxLogs'])->name('admin.logs.ajax');
    Route::get('/admin_dashboard/ajax/students', [AdminController::class, 'ajaxStudents'])->name('admin.students.ajax');
    Route::get('/admin_dashboard/ajax/departments', [AdminController::class, 'ajaxDepartments'])->name('admin.departments.ajax');

    // Complaint management routes
    Route::delete('/admin/complaint/{id}', [AdminController::class, 'deleteComplaint'])->name('admin.complaint.delete');
    Route::put('/admin/complaints/update/{id}', [AdminController::class, 'updateComplaintStatus'])->name('admin.complaints.updateStatus');
    Route::get('/admin/complaint-counts', [AdminController::class, 'getComplaintCounts'])->name('admin.complaints.counts');
    Route::get('/admin/complaint/{id}/details', [AdminController::class, 'getComplaintDetails'])->name('admin.complaint.details');

    // Student management route
    Route::put('/admin/student/revoke/{id}', [AdminController::class, 'revokeStudent'])->name('admin.student.revoke');

    // Department management routes
    Route::post('/admin/department/store', [AdminController::class, 'storeDepartment'])->name('admin.department.store');
    Route::get('/admin/department/edit/{id}', [AdminController::class, 'editDepartment'])->name('admin.department.edit');
    Route::post('/admin/department/update/{id}', [AdminController::class, 'updateDepartment'])->name('admin.department.update');

    // Excel Export Route
    Route::get('/admin/complaints/export', [AdminController::class, 'exportComplaintsToExcel'])->name('admin.complaints.export');



});
