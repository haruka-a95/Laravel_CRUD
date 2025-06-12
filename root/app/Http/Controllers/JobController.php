<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::orderByDesc('id')->paginate(20);
        return view('admin.jobs.index', [
            'jobs'=> $jobs,
        ]);
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(StoreJobRequest $request)
    {
        //新規登録
        $job = Job::create([
            'name'=> $request->name
        ]);
        return redirect(
            route('admin.jobs.show', ['job'=> $job])
        )->with('messages.success', '新規登録が完了しました。');
    }

    public function show(Job $job)
    {
        return view('admin.jobs.show', [
            'job'=>$job,
        ]);
    }

    public function edit(Job $job)
    {
        return view('admin.jobs.edit', [
            'job' => $job,
        ]);
    }

    public function confirm(UpdateJobRequest $request, Job $job)
    {
        $job->name = $request->name;
        return view('admin.jobs.confirm', [
            'job' => $job,
        ]);
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        $job->name = $request->name;
        $job->update();
        return redirect(
            route('admin.jobs.show', ['job'=>$job])
        )->with('messages.success', '更新が完了しました。');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect(route('admin.jobs.index'));
    }

    public function downloadCsv()
    {
        $csvRecords = self::getJobCsvRecords();
        return self::streamDownloadCsv('jobs.csv', $csvRecords);
    }

    public function downloadTsv()
    {
        $tsvRecords = self::getJobCsvRecords();
        $separator = "\t";
        return self::streamDownloadCsv('jobs.tsv', $tsvRecords, $separator);
    }

    private static function getJobCsvRecords():array
    {
        $jobs = Job::orderByDesc('id')->get();
        $csvRecords = [
            ['ID', '名称']//header
        ];
        foreach ($jobs as $job) {
            $csvRecords[] = [$job->id, $job->name];
        }
        return $csvRecords;
    }

    private static function streamDownloadCsv(
        string $name,
        iterable $fieldList,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '\\',
        string $eol = "\r\n"
    ){
        $contentType = 'text/plain';
        if ($separator === ',') {
            $contentType = 'text/csv';
        } elseif ($separator === "/t"){
            $contentType = 'text/tab-separated-values';
        }
        $headers = ['Content-Type' => $contentType];

        return response()->streamDownload(function () use ($fieldList, $separator, $enclosure, $escape, $eol){
            $stream = fopen('php://output', 'w');
            foreach ($fieldList as $fields){
                fputcsv($stream, $fields, $separator, $enclosure, $escape, $eol);
            }
            fclose($stream);
        }, $name, $headers);
    }
}
