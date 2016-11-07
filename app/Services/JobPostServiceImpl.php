<?php
/**
 * Created by PhpStorm.
 * User: Anik Dey
 * Date: 10/31/2016
 * Time: 3:45 PM
 */

namespace App\Services;


use App\Model\JobPost;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;
class JobPostServiceImpl implements JobPostService {

    private $jobPost;

    public function __construct(JobPost $jobPost)
    {
        $this->jobPost = $jobPost;
    }

    public function getAllJobPost() {
        return $this->jobPost->get();
    }

    public function getJobPostWithPagination($itemsPerPage){
        return $this->jobPost->paginate($itemsPerPage);
    }

    public function saveJobPost(Request $request){
        $this->jobPost->autoGeneratedJobId = $this->generateUniqueJobPostId();
        $this->jobPost->jobTitle = $request->input('jobTitle');
        $this->jobPost->jobDescription = $request->input('jobDescription');
        $this->jobPost->deadline = $request->input('deadline');
        $this->jobPost->departmentId = $request->input('departmentId');
        $this->jobPost->save();
    }

    public function findJobPostById($id){
        return $this->jobPost->find($id);
    }

    public function findJobPostByAutoGeneratedId($jobAutoGeneratedId){
        return $this->jobPost->where('autoGeneratedJobId', $jobAutoGeneratedId)->first();
    }

    public function getAllJobPostByDepartmentIdWithPagination($departmentAutoGeneratedId, $itemsPerPage){
        return $this->jobPost->where('departmentId', $departmentAutoGeneratedId)->paginate($itemsPerPage);
    }

    public function getAllJobPostByDepartmentId($departmentAutoGeneratedId){
        return $this->jobPost->where('departmentId', $departmentAutoGeneratedId)->get();
    }

    public function updateJobPostById($id, Request $request){
        $jobPost = $this->findJobPostById($id);
        $jobPost->jobTitle = $request->input('jobTitle');
        $jobPost->jobDescription = $request->input('jobDescription');
        $jobPost->deadline = $request->input('deadline');
        $jobPost->departmentId = $request->input('departmentId');
        $jobPost->save();
    }


    public function ajaxSearchJobPost(Request $request){
        $jobTitle = $request->input('jobTitle');
        $departmentId = $request->input('departmentId');
        $deadLineFrom = $request->input('deadLineFrom');
        $deadLineTo = $request->input('deadLineTo');
        return $this->jobPost->where('jobTitle', 'like', "%".$jobTitle."%")
            ->where('departmentId', 'like', "%".$departmentId."%")
            ->whereBetween('deadline', array($deadLineFrom, $deadLineTo))
            ->get();
    }

    public function deleteJobPostById($id){
        $jobPost = $this->findJobPostById($id);
        $jobPost->delete($id);
    }

    public function getLastInsertedId(){
        $last_id = DB::table('job_posts')->where('id',DB::raw("(select max(`id`) from job_posts)"))->first();
        if($last_id){
            $id = $last_id->id+1;
            return $id;
        }else{
            $id =1;
            return $id;
        }
    }

    public function generateUniqueJobPostId(){
        $generatedId = "JOB-";
        for($i=strlen($this->getLastInsertedId()); $i <= 5; $i++) {
            $generatedId .=0;
        }
        return $generatedId;
    }
} 