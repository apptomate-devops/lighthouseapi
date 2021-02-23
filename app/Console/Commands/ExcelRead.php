<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Excel;
use App\Imports\ReadExcel;
use App\Imports\ReadStatistics;
use App\Imports\ReadCumulative_pb;
use App\Imports\ReadReturnsPB;
use App\Imports\ReadDrawdown;
use App\Imports\ReadMonthlyReturns;
use App\Imports\ReadReturnsA;
use App\Imports\ReadStatisticsA;
use App\Imports\Readcrisis;
use App\Imports\ReadCumulative_a;
use App\Imports\ReadStressTestNegative;
use App\Imports\ReadStressTestPositive;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use DB;
use File;
use App\User;

class ExcelRead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read excel sheet in every minute';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
//      $filename='';
// //     $filepath = public_path().'/output';

//       $filepath = '/test/output/';
//       $files =  \Storage::disk('custom_folder_1')->files($filepath);
//       dd($files);

//   //   $files = File::files($filepath);
//      foreach($files as $data)
//      {
//       $filename=$data->getFilename();
//       $string = Str::of($filename)->basename('.xlsx');
//       $portfolio_record = DB::table('lc_portfolio')->where('portfolio_eventid','=',$string)->first();
//       session()->put('curr_portfolio_id', $portfolio_record->portfolio_id);
//       session()->put('curr_portfolio_user', $portfolio_record->portfolio_user);
//      }
//       if($filename != '')
//        {
//       $path =public_path().'/output/'.$filename;
//       DB::beginTransaction();
//       $data=Excel::import(new ReadExcel, $path);
//       $data=Excel::import(new ReadStatistics, $path);
//       $data=Excel::import(new ReadCumulative_pb, $path);
//       $data=Excel::import(new ReadReturnsPB, $path);
//       $data=Excel::import(new ReadMonthlyReturns, $path);
//       $data=Excel::import(new ReadReturnsA, $path);
//       $data=Excel::import(new ReadStatisticsA, $path);
//       $data=Excel::import(new ReadDrawdown, $path);
//       $data=Excel::import(new Readcrisis, $path);
//       $data=Excel::import(new ReadCumulative_a, $path);
//       $data=Excel::import(new ReadStressTestNegative, $path);
//       $data=Excel::import(new ReadStressTestPositive, $path);

   
//       $destinationPath = public_path('output/archive/'.$filename);
//       $move = File::move($path, $destinationPath);

//       DB::commit();
//         echo "Data added successfully";
//      }
//      else
//      {
//       echo "No files found";
//      }

     //   $filename = '';

     // $is_file = DB::table('lc_altsoft_job_queue')->where('job_status','=',2)->where('excel_status','=',0)->first();
     // if($is_file)
     //  {
     //  $portfolio_record = DB::table('lc_portfolio')->where('portfolio_id','=',$is_file->portfolio_id)->first();
     //  session()->put('curr_portfolio_id', $portfolio_record->portfolio_id);
     //  session()->put('curr_portfolio_user', $portfolio_record->portfolio_user);
     //  $filename = $is_file->output_excel;
     //  $path ='D:/test/output/'.$filename;
     //  DB::beginTransaction();
     //  $data=Excel::import(new ReadDrawdown, $path);
     //  $data=Excel::import(new ReadExcel, $path);
     //  $data=Excel::import(new ReadStatistics, $path);
     //  $data=Excel::import(new ReadCumulative_pb, $path);
     //  $data=Excel::import(new ReadReturnsPB, $path);
     //  $data=Excel::import(new ReadMonthlyReturns, $path);
     //  $data=Excel::import(new ReadReturnsA, $path);
     //  $data=Excel::import(new ReadStatisticsA, $path);
     //  $data=Excel::import(new Readcrisis, $path);
     //  $data=Excel::import(new ReadCumulative_a, $path);
     //  $data=Excel::import(new ReadStressTestNegative, $path);
     //  $data=Excel::import(new ReadStressTestPositive, $path);

   
     //  $destinationPath = 'D:/test/output/archive/'.$filename;
     //  $move = File::move($path, $destinationPath);

     //   DB::table('lc_altsoft_job_queue')->where('id',$is_file->id )->update(
     //   ['excel_status' => "1"]);

     //   DB::table('lc_portfolio')->where('portfolio_id','=',$is_file->portfolio_id)->update(
     //   ['portfolio_status' => "success"]);

     //  DB::commit();
     //    echo "Data added successfully";
     // }
     // else
     // {
     //  echo "No files found";
     // }
  
   $result =  User::import2();
    }
}
