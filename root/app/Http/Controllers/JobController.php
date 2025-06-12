<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        // 一覧画面
        //   id 降順でレコードセットを取得(Illuminate\Pagination\LengthAwarePaginator)
        $jobs = Job::orderByDesc('id')->paginate(20);
        return view('admin.jobs.index', [
            'jobs' => $jobs,
        ]);
    }


    public function create()
    {
        //新規画面
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
        //詳細画面
        return view('admin.jobs.show', [
            'job'=> $job,
        ]);
    }

    public function edit(Job $job)
    {
        //編集画面
        return view('admin.jobs.edit', [
            'job'=> $job,
        ]);
    }

    public function confirm(UpdateJobRequest $request, Job $job)
    {
        // 更新確認画面
        $job->name = $request->name;
        return view('admin.jobs.confirm', [
            'job' => $job,
        ]);
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        //更新
        $job->name = $request->name;
        $job->update();
        return redirect(
            route('admin.jobs.show', ['job'=>$job])
        )->with('messages.success', '更新が完了しました。');
    }

    public function destroy(Job $job)
    {
        //削除
        $job->delete();
        return redirect(route('admin.jobs.index'));
    }

    public function downloadCsv()
    {
        $csvRecords = self::getJobCsvRecords();
        return self::streamDownloadCsv('jobs.csv', $csvRecords);
    }

    private static function getJobCsvRecords():array//headerつき全件
    {
        $jobs = Job::orderByDesc('id')->get();//ID順に全件取得
        $csvRecords = [
            ['ID', '名称'], //ヘッダー
        ];
        foreach($jobs as $job){
            $csvRecords[] = [$job->id, $job->name];
        }
        return $csvRecords;

    }

    private static function streamDownloadCsv(
        string $name,
        iterable $fieldsList,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = "\\",
        string $eol = "\r\n"
    ){
        $contentType = 'text/plain'; // テキストファイル
        if ($separator === ',') {
            $contentType = 'text/csv';
        } elseif ($separator === '\t'){
            $contentType = 'text/tab-separated-values';
        }
        $headers = ['Content-Type'=>$contentType];

        return response()->streamDownload(function() use ($fieldsList, $separator, $enclosure, $escape, $eol){
            $stream = fopen('php://output', 'w');
            // ↓UTF-8 BOMを出力（Excel対策）
            fwrite($stream, "\xEF\xBB\xBF");
            foreach($fieldsList as $field){
                fputcsv($stream, $field, $separator, $enclosure, $escape, $eol);
            }
            fclose($stream);
        }, $name, $headers);
    }

    public function downloadTsv()
    {
        $csvRecords = self::getJobCsvRecords();
        return self::streamDownloadTsv('jobs.tsv', $csvRecords);
    }

    private static function streamDownloadTsv(
        string $name,
        iterable $records,
        string $eol = "\r\n"
    ){
        $headers = ['Content-type' => 'text/tab-separated-values'];

        return response()->streamDownload(function () use ($records, $eol){
            $stream = fopen('php://output', 'w');

            //Excel用
            fwrite($stream, "\xEF\xBB\xBF");

            foreach ($records as $row) {
                //タブと改行を置換
                $escaped = array_map(
                    fn($v)=>str_replace(["\t", "\r", "\n"], [' ', '', ' '], $v), $row);//それぞれ空文字に

                    //tab区切りを書き込み
                    fwrite($stream, implode("\t", $escaped) . $eol);
            }
            fclose($stream);
        }, $name, $headers);
    }
}