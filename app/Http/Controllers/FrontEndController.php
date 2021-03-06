<?php

namespace App\Http\Controllers;

use App\Services\ApplicantService;
use App\Services\JobPostService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FrontEndController extends Controller {

    private $jobPostService;
    private $applicantService;

    public function __construct(JobPostService $jobPostService, ApplicantService $applicantService)
    {
        $this->jobPostService = $jobPostService;
        $this->applicantService = $applicantService;
    }

    public function homePage() {
        $jobPosts = $this->jobPostService->getJobPostWithPagination(10);
        $sidebar = view('frontInc.sidebar');
        $header = view('frontInc.header');
        $footer = view('frontInc.footer');
        return view("front.home")
            ->with('header', $header)
            ->with('sidebar', $sidebar)
            ->with('footer', $footer)
            ->with('jobPosts', $jobPosts)
            ;
    }

    public function showApplicationForm($jobId) {
        $jobPost = $this->jobPostService->findJobPostByAutoGeneratedId($jobId);
        $sidebar = view('frontInc.sidebar');
        $header = view('frontInc.header');
        $footer = view('frontInc.footer');
        return view("front.applicationForm")
            ->with('header', $header)
            ->with('sidebar', $sidebar)
            ->with('footer', $footer)
            ->with('jobPost', $jobPost)
            ;
    }

    public function saveApplication($jobId, Request $request) {
        $rules = array(
            'fullName' => 'required',
            'jobId' => 'required',
            'departmentId' => 'required',
            'mobileNumber' => 'required',
            'email' => 'required',
            'address' => 'required',
            'expectedSalary' => 'required',
            'picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'cv' => 'required|mimes:pdf|max:1024',
        );
        $messages = array(
            'fullName.required' => 'Full Name is required.',
            'jobId.required' => 'Job is is required.',
            'expectedSalary.required' => 'Expected salary is required.',
            'departmentId.required' => 'Department id is required.',
            'mobileNumber.required' => 'Mobile Number is required.',
            'email.required' => 'Email is required.',
            'address.required' => 'Address is required.',
            'picture.image' => 'Only image is allowed.',
            'picture.required' => 'Please upload a recent picture.',
            'picture.mimes' => 'Only JPEG,JPG, PNG.',
            'picture.max' => 'Max 2MB',
            'cv.required' => 'Please attach your updated cv.',
            'cv.mimes' => 'Only pdf.',
            'cv.max' => 'Max 1MB',
        );
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return Redirect::to('/apply-to-job/'.$jobId)->withErrors($validator)->withInput();
        } else {
            $this->applicantService->saveApplication($request);
            Session::flash('message', 'Successfully submitted your application.');
            return Redirect::to('/apply-to-job/'.$jobId);
        }
    }



    public function showDepartmentWiseJob($departmentAutoGeneratedId) {

        $jobPosts = $this->jobPostService->getAllJobPostByDepartmentIdWithPagination($departmentAutoGeneratedId, 10);
        $sidebar = view('frontInc.sidebar');
        $header = view('frontInc.header');
        $footer = view('frontInc.footer');
        return view("front.departmentWiseJob")
            ->with('header', $header)
            ->with('sidebar', $sidebar)
            ->with('footer', $footer)
            ->with('jobPosts', $jobPosts)
            ;
    }

    public function showJobDetail($jobAutoGeneratedId) {

        $jobPost = $this->jobPostService->findJobPostByAutoGeneratedId($jobAutoGeneratedId);
        $sidebar = view('frontInc.sidebar');
        $header = view('frontInc.header');
        $footer = view('frontInc.footer');
        return view("front.jobDetail")
            ->with('header', $header)
            ->with('sidebar', $sidebar)
            ->with('footer', $footer)
            ->with('jobPost', $jobPost)
            ;
    }




}
