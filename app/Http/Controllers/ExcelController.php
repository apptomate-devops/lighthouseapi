<?php

namespace App\Http\Controllers;
Use Excel;
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
use Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ExcelController extends Controller
{
   public function import(Request $request){

       $output_excel_path = 'file://10.10.0.7/Navigator/Output/Archive/lc1202202100001.xlsx';
       DB::table('lc_portfolio')->where('portfolio_id','=',1)->update([
        'portfolio_status' => "success",
        'portfolio_output_excel'=>$output_excel_path
        ]);

       exit;

       // ini_set('memory_limit',-1);
       // ini_set('max_execution_time', 0);
       $filename = '';

    $is_file = DB::table('lc_altsoft_job_queue')->where('job_status','=',2)->where('excel_status','=',0)->first();
    if($is_file)
     {
     $portfolio_record = DB::table('lc_portfolio')->where('portfolio_id','=',$is_file->portfolio_id)->first();
       session()->put('curr_portfolio_id', $portfolio_record->portfolio_id);
       session()->put('curr_portfolio_user', $portfolio_record->portfolio_user);

      $filename = $is_file->output_excel;
     
      DB::beginTransaction();
      $data=Excel::import(new ReadDrawdown, $path);
      $data=Excel::import(new ReadExcel, $path);
      $data=Excel::import(new ReadStatistics, $path);
      $data=Excel::import(new ReadCumulative_pb, $path);
      $data=Excel::import(new ReadReturnsPB, $path);
      $data=Excel::import(new ReadMonthlyReturns, $path);
      $data=Excel::import(new ReadReturnsA, $path);
      $data=Excel::import(new ReadStatisticsA, $path);
      $data=Excel::import(new Readcrisis, $path);
      $data=Excel::import(new ReadCumulative_a, $path);
      DB::commit();
        echo "Data added successfully";
     }
     else
     {
      echo "No files found";
     }
    }

    public function import2(){

        // for ($i=0; $i < 231; $i++){ 
        //  echo "\"if(isset(\$row[$i]) && trim(\$row[$i])) \$row$i=trim(\$row[$i]);\"";
        //  echo "\$row".$i."=";
        //  echo "<br>";
        //   }

      //exit;

      require('xl/excel_reader2.php');
      require('xl/SpreadsheetReader_XLSX.php');
      require('xl/SpreadsheetReader.php');
   
      $filename = '';

      $fail_rec = DB::table('lc_altsoft_job_queue')->where('job_status','=',3)->where('excel_status','=',0)->get();
      if($fail_rec)
      {
      foreach($fail_rec as $rec)
      {
       $mask_data = str_replace('Jrakra@#1449', '****', $rec->job_log);
        DB::table('lc_altsoft_job_queue')->where('portfolio_id',$rec->portfolio_id )->update(
       ['excel_status' => "2"]);

      $port_rec =  DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->first();

      if($port_rec->portfolio_previous_status == '')
      {
       DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->update(
       ['portfolio_status' => "failed",
        'portfolio_error' => $mask_data]); 
      }
      if($port_rec->portfolio_previous_status == 'success')
      {
        DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->update(
       ['portfolio_status' => "failed-previous-success",
        'portfolio_error' => $mask_data]); 
      }
      if($port_rec->portfolio_previous_status == 'failed')
      {
        DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->update(
       ['portfolio_status' => "failed-previous-failed",
        'portfolio_error' => $mask_data]); 
      }
        if($port_rec->portfolio_previous_status == 'failed-previous-success')
      {
        DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->update(
       ['portfolio_status' => "failed-previous-success",
        'portfolio_error' => $mask_data]); 
      }
        if($port_rec->portfolio_previous_status == 'failed-previous-failed')
      {
        DB::table('lc_portfolio')->where('portfolio_id','=',$rec->portfolio_id)->update(
       ['portfolio_status' => "failed-previous-failed",
        'portfolio_error' => $mask_data]); 
      }
      }  
      }


      $is_file = DB::table('lc_altsoft_job_queue')->where('job_status','=',2)->where('excel_status','=',0)->first();
      if($is_file)
      {
      DB::beginTransaction(); 

    DB::table('lc_crisis')->where('crisis_portfolio_id',$is_file->portfolio_id)->delete();
    DB::table('lc_cumulative_returns_a')->where('cuma_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_cumulative_returns_pb')->where('cumpb_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_drawdown')->where('drawdown_portfolio_id',$is_file->portfolio_id)->delete();
    DB::table('lc_monthlyreturns')->where('monreturns_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_pba_summary')->where('pbasummary_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_returns_a')->where('returnsa_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_returns_pb')->where('returnspb_portfolioid',$is_file->portfolio_id)->delete();
    DB::table('lc_statistics_a')->where('sta_a_portfolio_id',$is_file->portfolio_id)->delete();
    DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id',$is_file->portfolio_id)->delete();
    DB::table('lc_stress_test_negative')->where('stn_portfolio_id',$is_file->portfolio_id)->delete();
    DB::table('lc_stress_test_positive')->where('stp_portfolio_id',$is_file->portfolio_id)->delete();

       $portfolio_record = DB::table('lc_portfolio')->where('portfolio_id','=',$is_file->portfolio_id)->first();
       $portfolio_id = $portfolio_record->portfolio_id;
       $portfolio_user= $portfolio_record->portfolio_user;
       $filename = $is_file->output_excel;
       $path =public_path().'/output/'.$filename;
     
       $Reader = new \SpreadsheetReader($path);
       $Sheets = $Reader -> Sheets();

       foreach ($Sheets as $Index => $Name){
        echo 'Sheet #'.$Index.': '.$Name;

        $Reader -> ChangeSheet($Index);
        $status=0;
        $check = 0;
        foreach ($Reader as $key => $row)
        {
      
  $row0=$row1=$row2=$row3=$row4=$row5=$row6=$row7=$row8=$row9=$row10=$row11=$row12=$row13=$row14=$row15=$row16=$row17=$row18=$row19=$row20=$row21=$row22=$row23=$row24=$row25=$row26=$row27=$row28=$row29=$row30=$row31=$row32=$row33=$row34=$row35=$row36=$row37=$row38=$row39=$row40=$row41=$row42=$row43=$row44=$row45=$row46=$row47=$row48=$row49=$row50=$row51=$row52=$row53=$row54=$row55=$row56=$row57=$row58=$row59=$row60=$row61=$row62=$row63=$row64=$row65=$row66=$row67=$row68=$row69=$row70=$row71=$row72=$row73=$row74=$row75=$row76=$row77=$row78=$row79=$row80=$row81=$row82=$row83=$row84=$row85=$row86=$row87=$row88=$row89=$row90=$row91=$row92=$row93=$row94=$row95=$row96=$row97=$row98=$row99=$row100=$row101=$row102=$row103=$row104=$row105=$row106=$row107=$row108=$row109=$row110=$row111=$row112=$row113=$row114=$row115=$row116=$row117=$row118=$row119=$row120=$row121=$row122=$row123=$row124=$row125=$row126=$row127=$row128=$row129=$row130=$row131=$row132=$row133=$row134=$row135=$row136=$row137=$row138=$row139=$row140=$row141=$row142=$row143=$row144=$row145=$row146=$row147=$row148=$row149=$row150=$row151=$row152=$row153=$row154=$row155=$row156=$row157=$row158=$row159=$row160=$row161=$row162=$row163=$row164=$row165=$row166=$row167=$row168=$row169=$row170=$row171=$row172=$row173=$row174=$row175=$row176=$row177=$row178=$row179=$row180=$row181=$row182=$row183=$row184=$row185=$row186=$row187=$row188=$row189=$row190=$row191=$row192=$row193=$row194=$row195=$row196=$row197=$row198=$row199=$row200=$row201=$row202=$row203=$row204=$row205=$row206=$row207=$row208=$row209=$row210=$row211=$row212=$row213=$row214=$row215=$row216=$row217=$row218=$row219=$row220=$row221=$row222=$row223=$row224=$row225=$row226=$row227=$row228=$row229=$row230="";

if(isset($row[0]) && trim($row[0])) $row0=trim($row[0]);
if(isset($row[1]) && trim($row[1])) $row1=trim($row[1]);
if(isset($row[2]) && trim($row[2])) $row2=trim($row[2]);
if(isset($row[3]) && trim($row[3])) $row3=trim($row[3]);
if(isset($row[4]) && trim($row[4])) $row4=trim($row[4]);
if(isset($row[5]) && trim($row[5])) $row5=trim($row[5]);
if(isset($row[6]) && trim($row[6])) $row6=trim($row[6]);
if(isset($row[7]) && trim($row[7])) $row7=trim($row[7]);
if(isset($row[8]) && trim($row[8])) $row8=trim($row[8]);
if(isset($row[9]) && trim($row[9])) $row9=trim($row[9]);
if(isset($row[10]) && trim($row[10])) $row10=trim($row[10]);
if(isset($row[11]) && trim($row[11])) $row11=trim($row[11]);
if(isset($row[12]) && trim($row[12])) $row12=trim($row[12]);
if(isset($row[13]) && trim($row[13])) $row13=trim($row[13]);
if(isset($row[14]) && trim($row[14])) $row14=trim($row[14]);
if(isset($row[15]) && trim($row[15])) $row15=trim($row[15]);
if(isset($row[16]) && trim($row[16])) $row16=trim($row[16]);
if(isset($row[17]) && trim($row[17])) $row17=trim($row[17]);
if(isset($row[18]) && trim($row[18])) $row18=trim($row[18]);
if(isset($row[19]) && trim($row[19])) $row19=trim($row[19]);
if(isset($row[20]) && trim($row[20])) $row20=trim($row[20]);
if(isset($row[21]) && trim($row[21])) $row21=trim($row[21]);
if(isset($row[22]) && trim($row[22])) $row22=trim($row[22]);
if(isset($row[23]) && trim($row[23])) $row23=trim($row[23]);
if(isset($row[24]) && trim($row[24])) $row24=trim($row[24]);
if(isset($row[25]) && trim($row[25])) $row25=trim($row[25]);
if(isset($row[26]) && trim($row[26])) $row26=trim($row[26]);
if(isset($row[27]) && trim($row[27])) $row27=trim($row[27]);
if(isset($row[28]) && trim($row[28])) $row28=trim($row[28]);
if(isset($row[29]) && trim($row[29])) $row29=trim($row[29]);
if(isset($row[30]) && trim($row[30])) $row30=trim($row[30]);
if(isset($row[31]) && trim($row[31])) $row31=trim($row[31]);
if(isset($row[32]) && trim($row[32])) $row32=trim($row[32]);
if(isset($row[33]) && trim($row[33])) $row33=trim($row[33]);
if(isset($row[34]) && trim($row[34])) $row34=trim($row[34]);
if(isset($row[35]) && trim($row[35])) $row35=trim($row[35]);
if(isset($row[36]) && trim($row[36])) $row36=trim($row[36]);
if(isset($row[37]) && trim($row[37])) $row37=trim($row[37]);
if(isset($row[38]) && trim($row[38])) $row38=trim($row[38]);
if(isset($row[39]) && trim($row[39])) $row39=trim($row[39]);
if(isset($row[40]) && trim($row[40])) $row40=trim($row[40]);
if(isset($row[41]) && trim($row[41])) $row41=trim($row[41]);
if(isset($row[42]) && trim($row[42])) $row42=trim($row[42]);
if(isset($row[43]) && trim($row[43])) $row43=trim($row[43]);
if(isset($row[44]) && trim($row[44])) $row44=trim($row[44]);
if(isset($row[45]) && trim($row[45])) $row45=trim($row[45]);
if(isset($row[46]) && trim($row[46])) $row46=trim($row[46]);
if(isset($row[47]) && trim($row[47])) $row47=trim($row[47]);
if(isset($row[48]) && trim($row[48])) $row48=trim($row[48]);
if(isset($row[49]) && trim($row[49])) $row49=trim($row[49]);
if(isset($row[50]) && trim($row[50])) $row50=trim($row[50]);
if(isset($row[51]) && trim($row[51])) $row51=trim($row[51]);
if(isset($row[52]) && trim($row[52])) $row52=trim($row[52]);
if(isset($row[53]) && trim($row[53])) $row53=trim($row[53]);
if(isset($row[54]) && trim($row[54])) $row54=trim($row[54]);
if(isset($row[55]) && trim($row[55])) $row55=trim($row[55]);
if(isset($row[56]) && trim($row[56])) $row56=trim($row[56]);
if(isset($row[57]) && trim($row[57])) $row57=trim($row[57]);
if(isset($row[58]) && trim($row[58])) $row58=trim($row[58]);
if(isset($row[59]) && trim($row[59])) $row59=trim($row[59]);
if(isset($row[60]) && trim($row[60])) $row60=trim($row[60]);
if(isset($row[61]) && trim($row[61])) $row61=trim($row[61]);
if(isset($row[62]) && trim($row[62])) $row62=trim($row[62]);
if(isset($row[63]) && trim($row[63])) $row63=trim($row[63]);
if(isset($row[64]) && trim($row[64])) $row64=trim($row[64]);
if(isset($row[65]) && trim($row[65])) $row65=trim($row[65]);
if(isset($row[66]) && trim($row[66])) $row66=trim($row[66]);
if(isset($row[67]) && trim($row[67])) $row67=trim($row[67]);
if(isset($row[68]) && trim($row[68])) $row68=trim($row[68]);
if(isset($row[69]) && trim($row[69])) $row69=trim($row[69]);
if(isset($row[70]) && trim($row[70])) $row70=trim($row[70]);
if(isset($row[71]) && trim($row[71])) $row71=trim($row[71]);
if(isset($row[72]) && trim($row[72])) $row72=trim($row[72]);
if(isset($row[73]) && trim($row[73])) $row73=trim($row[73]);
if(isset($row[74]) && trim($row[74])) $row74=trim($row[74]);
if(isset($row[75]) && trim($row[75])) $row75=trim($row[75]);
if(isset($row[76]) && trim($row[76])) $row76=trim($row[76]);
if(isset($row[77]) && trim($row[77])) $row77=trim($row[77]);
if(isset($row[78]) && trim($row[78])) $row78=trim($row[78]);
if(isset($row[79]) && trim($row[79])) $row79=trim($row[79]);
if(isset($row[80]) && trim($row[80])) $row80=trim($row[80]);
if(isset($row[81]) && trim($row[81])) $row81=trim($row[81]);
if(isset($row[82]) && trim($row[82])) $row82=trim($row[82]);
if(isset($row[83]) && trim($row[83])) $row83=trim($row[83]);
if(isset($row[84]) && trim($row[84])) $row84=trim($row[84]);
if(isset($row[85]) && trim($row[85])) $row85=trim($row[85]);
if(isset($row[86]) && trim($row[86])) $row86=trim($row[86]);
if(isset($row[87]) && trim($row[87])) $row87=trim($row[87]);
if(isset($row[88]) && trim($row[88])) $row88=trim($row[88]);
if(isset($row[89]) && trim($row[89])) $row89=trim($row[89]);
if(isset($row[90]) && trim($row[90])) $row90=trim($row[90]);
if(isset($row[91]) && trim($row[91])) $row91=trim($row[91]);
if(isset($row[92]) && trim($row[92])) $row92=trim($row[92]);
if(isset($row[93]) && trim($row[93])) $row93=trim($row[93]);
if(isset($row[94]) && trim($row[94])) $row94=trim($row[94]);
if(isset($row[95]) && trim($row[95])) $row95=trim($row[95]);
if(isset($row[96]) && trim($row[96])) $row96=trim($row[96]);
if(isset($row[97]) && trim($row[97])) $row97=trim($row[97]);
if(isset($row[98]) && trim($row[98])) $row98=trim($row[98]);
if(isset($row[99]) && trim($row[99])) $row99=trim($row[99]);
if(isset($row[100]) && trim($row[100])) $row100=trim($row[100]);
if(isset($row[101]) && trim($row[101])) $row101=trim($row[101]);
if(isset($row[102]) && trim($row[102])) $row102=trim($row[102]);
if(isset($row[103]) && trim($row[103])) $row103=trim($row[103]);
if(isset($row[104]) && trim($row[104])) $row104=trim($row[104]);
if(isset($row[105]) && trim($row[105])) $row105=trim($row[105]);
if(isset($row[106]) && trim($row[106])) $row106=trim($row[106]);
if(isset($row[107]) && trim($row[107])) $row107=trim($row[107]);
if(isset($row[108]) && trim($row[108])) $row108=trim($row[108]);
if(isset($row[109]) && trim($row[109])) $row109=trim($row[109]);
if(isset($row[110]) && trim($row[110])) $row110=trim($row[110]);
if(isset($row[111]) && trim($row[111])) $row111=trim($row[111]);
if(isset($row[112]) && trim($row[112])) $row112=trim($row[112]);
if(isset($row[113]) && trim($row[113])) $row113=trim($row[113]);
if(isset($row[114]) && trim($row[114])) $row114=trim($row[114]);
if(isset($row[115]) && trim($row[115])) $row115=trim($row[115]);
if(isset($row[116]) && trim($row[116])) $row116=trim($row[116]);
if(isset($row[117]) && trim($row[117])) $row117=trim($row[117]);
if(isset($row[118]) && trim($row[118])) $row118=trim($row[118]);
if(isset($row[119]) && trim($row[119])) $row119=trim($row[119]);
if(isset($row[120]) && trim($row[120])) $row120=trim($row[120]);
if(isset($row[121]) && trim($row[121])) $row121=trim($row[121]);
if(isset($row[122]) && trim($row[122])) $row122=trim($row[122]);
if(isset($row[123]) && trim($row[123])) $row123=trim($row[123]);
if(isset($row[124]) && trim($row[124])) $row124=trim($row[124]);
if(isset($row[125]) && trim($row[125])) $row125=trim($row[125]);
if(isset($row[126]) && trim($row[126])) $row126=trim($row[126]);
if(isset($row[127]) && trim($row[127])) $row127=trim($row[127]);
if(isset($row[128]) && trim($row[128])) $row128=trim($row[128]);
if(isset($row[129]) && trim($row[129])) $row129=trim($row[129]);
if(isset($row[130]) && trim($row[130])) $row130=trim($row[130]);
if(isset($row[131]) && trim($row[131])) $row131=trim($row[131]);
if(isset($row[132]) && trim($row[132])) $row132=trim($row[132]);
if(isset($row[133]) && trim($row[133])) $row133=trim($row[133]);
if(isset($row[134]) && trim($row[134])) $row134=trim($row[134]);
if(isset($row[135]) && trim($row[135])) $row135=trim($row[135]);
if(isset($row[136]) && trim($row[136])) $row136=trim($row[136]);
if(isset($row[137]) && trim($row[137])) $row137=trim($row[137]);
if(isset($row[138]) && trim($row[138])) $row138=trim($row[138]);
if(isset($row[139]) && trim($row[139])) $row139=trim($row[139]);
if(isset($row[140]) && trim($row[140])) $row140=trim($row[140]);
if(isset($row[141]) && trim($row[141])) $row141=trim($row[141]);
if(isset($row[142]) && trim($row[142])) $row142=trim($row[142]);
if(isset($row[143]) && trim($row[143])) $row143=trim($row[143]);
if(isset($row[144]) && trim($row[144])) $row144=trim($row[144]);
if(isset($row[145]) && trim($row[145])) $row145=trim($row[145]);
if(isset($row[146]) && trim($row[146])) $row146=trim($row[146]);
if(isset($row[147]) && trim($row[147])) $row147=trim($row[147]);
if(isset($row[148]) && trim($row[148])) $row148=trim($row[148]);
if(isset($row[149]) && trim($row[149])) $row149=trim($row[149]);
if(isset($row[150]) && trim($row[150])) $row150=trim($row[150]);
if(isset($row[151]) && trim($row[151])) $row151=trim($row[151]);
if(isset($row[152]) && trim($row[152])) $row152=trim($row[152]);
if(isset($row[153]) && trim($row[153])) $row153=trim($row[153]);
if(isset($row[154]) && trim($row[154])) $row154=trim($row[154]);
if(isset($row[155]) && trim($row[155])) $row155=trim($row[155]);
if(isset($row[156]) && trim($row[156])) $row156=trim($row[156]);
if(isset($row[157]) && trim($row[157])) $row157=trim($row[157]);
if(isset($row[158]) && trim($row[158])) $row158=trim($row[158]);
if(isset($row[159]) && trim($row[159])) $row159=trim($row[159]);
if(isset($row[160]) && trim($row[160])) $row160=trim($row[160]);
if(isset($row[161]) && trim($row[161])) $row161=trim($row[161]);
if(isset($row[162]) && trim($row[162])) $row162=trim($row[162]);
if(isset($row[163]) && trim($row[163])) $row163=trim($row[163]);
if(isset($row[164]) && trim($row[164])) $row164=trim($row[164]);
if(isset($row[165]) && trim($row[165])) $row165=trim($row[165]);
if(isset($row[166]) && trim($row[166])) $row166=trim($row[166]);
if(isset($row[167]) && trim($row[167])) $row167=trim($row[167]);
if(isset($row[168]) && trim($row[168])) $row168=trim($row[168]);
if(isset($row[169]) && trim($row[169])) $row169=trim($row[169]);
if(isset($row[170]) && trim($row[170])) $row170=trim($row[170]);
if(isset($row[171]) && trim($row[171])) $row171=trim($row[171]);
if(isset($row[172]) && trim($row[172])) $row172=trim($row[172]);
if(isset($row[173]) && trim($row[173])) $row173=trim($row[173]);
if(isset($row[174]) && trim($row[174])) $row174=trim($row[174]);
if(isset($row[175]) && trim($row[175])) $row175=trim($row[175]);
if(isset($row[176]) && trim($row[176])) $row176=trim($row[176]);
if(isset($row[177]) && trim($row[177])) $row177=trim($row[177]);
if(isset($row[178]) && trim($row[178])) $row178=trim($row[178]);
if(isset($row[179]) && trim($row[179])) $row179=trim($row[179]);
if(isset($row[180]) && trim($row[180])) $row180=trim($row[180]);
if(isset($row[181]) && trim($row[181])) $row181=trim($row[181]);
if(isset($row[182]) && trim($row[182])) $row182=trim($row[182]);
if(isset($row[183]) && trim($row[183])) $row183=trim($row[183]);
if(isset($row[184]) && trim($row[184])) $row184=trim($row[184]);
if(isset($row[185]) && trim($row[185])) $row185=trim($row[185]);
if(isset($row[186]) && trim($row[186])) $row186=trim($row[186]);
if(isset($row[187]) && trim($row[187])) $row187=trim($row[187]);
if(isset($row[188]) && trim($row[188])) $row188=trim($row[188]);
if(isset($row[189]) && trim($row[189])) $row189=trim($row[189]);
if(isset($row[190]) && trim($row[190])) $row190=trim($row[190]);
if(isset($row[191]) && trim($row[191])) $row191=trim($row[191]);
if(isset($row[192]) && trim($row[192])) $row192=trim($row[192]);
if(isset($row[193]) && trim($row[193])) $row193=trim($row[193]);
if(isset($row[194]) && trim($row[194])) $row194=trim($row[194]);
if(isset($row[195]) && trim($row[195])) $row195=trim($row[195]);
if(isset($row[196]) && trim($row[196])) $row196=trim($row[196]);
if(isset($row[197]) && trim($row[197])) $row197=trim($row[197]);
if(isset($row[198]) && trim($row[198])) $row198=trim($row[198]);
if(isset($row[199]) && trim($row[199])) $row199=trim($row[199]);
if(isset($row[200]) && trim($row[200])) $row200=trim($row[200]);
if(isset($row[201]) && trim($row[201])) $row201=trim($row[201]);
if(isset($row[202]) && trim($row[202])) $row202=trim($row[202]);
if(isset($row[203]) && trim($row[203])) $row203=trim($row[203]);
if(isset($row[204]) && trim($row[204])) $row204=trim($row[204]);
if(isset($row[205]) && trim($row[205])) $row205=trim($row[205]);
if(isset($row[206]) && trim($row[206])) $row206=trim($row[206]);
if(isset($row[207]) && trim($row[207])) $row207=trim($row[207]);
if(isset($row[208]) && trim($row[208])) $row208=trim($row[208]);
if(isset($row[209]) && trim($row[209])) $row209=trim($row[209]);
if(isset($row[210]) && trim($row[210])) $row210=trim($row[210]);
if(isset($row[211]) && trim($row[211])) $row211=trim($row[211]);
if(isset($row[212]) && trim($row[212])) $row212=trim($row[212]);
if(isset($row[213]) && trim($row[213])) $row213=trim($row[213]);
if(isset($row[214]) && trim($row[214])) $row214=trim($row[214]);
if(isset($row[215]) && trim($row[215])) $row215=trim($row[215]);
if(isset($row[216]) && trim($row[216])) $row216=trim($row[216]);
if(isset($row[217]) && trim($row[217])) $row217=trim($row[217]);
if(isset($row[218]) && trim($row[218])) $row218=trim($row[218]);
if(isset($row[219]) && trim($row[219])) $row219=trim($row[219]);
if(isset($row[220]) && trim($row[220])) $row220=trim($row[220]);
if(isset($row[221]) && trim($row[221])) $row221=trim($row[221]);
if(isset($row[222]) && trim($row[222])) $row222=trim($row[222]);
if(isset($row[223]) && trim($row[223])) $row223=trim($row[223]);
if(isset($row[224]) && trim($row[224])) $row224=trim($row[224]);
if(isset($row[225]) && trim($row[225])) $row225=trim($row[225]);
if(isset($row[226]) && trim($row[226])) $row226=trim($row[226]);
if(isset($row[227]) && trim($row[227])) $row227=trim($row[227]);
if(isset($row[228]) && trim($row[228])) $row228=trim($row[228]);
if(isset($row[229]) && trim($row[229])) $row229=trim($row[229]);
if(isset($row[230]) && trim($row[230])) $row230=trim($row[230]);

         if($status == 1)
         {

        if($Index == 0)
         {

  if ( $row[4] != '') {
        $data=DB::table('lc_pba_summary')->insert([
          ['pbasummary_clientid' => $portfolio_user,'pbasummary_portfolioid' => $portfolio_id,'pbasummary_eventid'=>$row0,'pbasummary_timestamp'=>$row1,'pbasummary_portfolio'=>$row2,'pbasummary_benchmark'=>$row3,'pbasummary_asset'=>$row4,'pbasummary_startdate'=>$row5,'pbasummary_enddate'=>$row6,'pbasummary_riskfree'=>$row7,'pbasummary_managementfee'=>$row8,'pbasummary_addedon'=>Carbon::now(),'pbasummary_addedby'=>1,'pbasummary_updatedon'=>Carbon::now(),'pbasummary_updatedby'=>1]
            ]);
           }
         }

          if($Index == 1)
        {
  if ( $row[0] != '') {
           $data=DB::table('lc_monthlyreturns')->insert([
    ['monreturns_clientid' => $portfolio_user,'monreturns_portfolioid' => $portfolio_id,'monreturns_year'=>$row0,'monreturns_jan'=>$row1,'monreturns_feb'=>$row2,'monreturns_mar'=>$row3,'monreturns_apr'=>$row4,'monreturns_may'=>$row5,'monreturns_jun'=>$row6,'monreturns_jul'=>$row7,'monreturns_aug'=>$row8,'monreturns_sep'=>$row9,'monreturns_oct'=>$row10,'monreturns_nov'=>$row11,'monreturns_dec'=>$row12,'monreturns_total'=>$row13,'monreturns_addedon'=>Carbon::now(),'monreturns_addedby'=>1,'monreturns_updatedon'=>Carbon::now(),'monreturns_updatedby'=>1]
   ]);  
   }
      }

      if($Index == 2)
        {
           if ( $row[0] != '') {
   $data=DB::table('lc_returns_pb')->insert([
    ['returnspb_clientid' => $portfolio_user,'returnspb_portfolioid' => $portfolio_id,'returnspb_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row0)->format('m-Y'),'returnspb_lcbalanced'=>$row1,'returnspb_msci_acwi_index'=>$row2,'returnspb_globalequities'=>$row3,'returnspb_addedon'=>Carbon::now(),'returnspb_addedby'=>1,'returnspb_updatedon'=>Carbon::now(),'returnspb_updatedby'=>1]
   ]);
    }
   }
  
        if($Index == 3)
        {
           if ( $row[0] != '') {
           $data=DB::table('lc_returns_a')->insert([
    ['returnsa_clientid' => $portfolio_user,'returnsa_portfolioid' => $portfolio_id,'returnsa_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row0)->format('m-Y'),'returnsa_bernardfund'=>$row1,'returnsa_creditfund'=>$row2,'returnsa_investmentfund'=>$row3,'returnsa_asiafund'=>$row4,'returnsa_fundsicav'=>$row5,'returnsa_fundsp'=>$row6,'returnsa_equityfund'=>$row7,'returnsa_biotechnologyetf'=>$row8,'returnsa_goldetc'=>$row9,'returnsa_globalfund'=>$row10,'returnsa_igneofund'=>$row11,'returnsa_investmentas'=>$row12,'returnsa_hedgefund'=>$row13,'returnsa_growthfund'=>$row14,'returnsa_macrofund'=>$row15,'returnsa_harbortalf'=>$row16,'returnsa_addedon'=>Carbon::now(),'returnsa_addedby'=>1,'returnsa_updatedon'=>Carbon::now(),'returnsa_updatedby'=>1]
   ]);
    }
    }
      if($Index == 4)
        {
         if ( $row[0] != '') {
     $data=DB::table('lc_cumulative_returns_pb')->insert([
    ['cumpb_clientid' => $portfolio_user,'cumpb_portfolioid' => $portfolio_id,'cumpb_date'=>$row0,'cumpb_balancedportfolio'=>$row1,'cumpb_msci_aswi'=>$row2,'cumpb_globalequities'=>$row3,'cumpb_addedon'=>Carbon::now(),'cumpb_addedby'=>1,'cumpb_updatedon'=>Carbon::now(),'cumpb_updatedby'=>1]
     ]);      
        } 
      }
        if($Index == 5)
        {
          if ( $row[0] != '') {
           $data=DB::table('lc_cumulative_returns_a')->insert([
    ['cuma_portfolioid' => $portfolio_id,'cuma_clientid' => $portfolio_user,'cuma_date'=>$row0,'cuma_bernardfund'=>$row1,'cuma_creditfund'=>$row2,'cuma_investmentfund'=>$row3,'cuma_asiafund'=>$row4,'cuma_fundsicav'=>$row5,'cuma_fundsp'=>$row6,'cuma_equityfund'=>$row7,'cuma_biotechnologyetf'=>$row8,'cuma_goldetc'=>$row9,'cuma_globalfund'=>$row10,'cuma_igneofund'=>$row11,'cuma_invesmentas'=>$row12,'cuma_hedgefund'=>$row13,'cuma_growthfund'=>$row14,'cuma_macrofund'=>$row15,'cuma_harbortalf'=>$row16,'cuma_addedon'=>Carbon::now(),'cuma_addedby'=>1,'cuma_updatedon'=>Carbon::now(),'cuma_updatedby'=>1]
    ]);
       }
      }
        if($Index == 6)
        {
        if ( $row[0] != '') {
        $data=DB::table('lc_statistics_pb')->insert([
         [
        'sta_pb_assets'=>$row0,
        'sta_pb_weight'=>$row1,
        'sta_pb_expected_annual_return'=>$row2,
        'sta_pb_expected_annual_volatility'=>$row3,
        'sta_pb_expected_annual_drawdown'=>$row4,
        'sta_pb_his_annual_return_sin_asset_inception'=>$row5,
        'sta_pb_his_annual_return_in_time_window'=>$row6,
        'sta_pb_equilibrium_annual_mean'=>$row7,
        'sta_pb_annual_volatility_sin_asset_inception'=>$row8,
        'sta_pb_annual_volatility_in_time_window'=>$row9,
        'sta_pb_skewness'=>$row10,
        'sta_pb_excess_kurtosis'=>$row11,
        'sta_pb_last_1_month_return'=>$row12,
        'sta_pb_last_3_month_return'=>$row13,
        'sta_pb_last_12_month_return'=>$row14,
        'sta_pb_year_to_date_return'=>$row15,
        'sta_pb_annual_last_3_month_return'=>$row16,
        'sta_pb_annual_last_3_year_return'=>$row17,
        'sta_pb_annual_last_5_year_return'=>$row18,
        'sta_pb_annual_volatility_last_12_month'=>$row19,
        'sta_pb_annual_volatility_last_3_year'=>$row20,
        'sta_pb_annual_volatility_last_5_year'=>$row21,
        'sta_pb_normal_month_var_99'=>$row22,
        'sta_pb_modified_month_var_99'=>$row23,
        'sta_pb_conditional_month_var_99'=>$row24,
        'sta_pb_normal_month_var_97'=>$row25,
        'sta_pb_modified_month_var_97'=>$row26,
        'sta_pb_conditional_month_var_97'=>$row27,
        'sta_pb_normal_month_var_95'=>$row28,
        'sta_pb_modified_month_var_95'=>$row29,
        'sta_pb_conditional_month_var_95'=>$row30,
        'sta_pb_correlation_msci_index'=>$row31,
        'sta_pb_correlation_50_global_equities_50_lg'=>$row32,
        'sta_pb_bull_correlation_msci_index'=>$row33,
        'sta_pb_bear_correlation_msci_index'=>$row34,
        'sta_pb_bull_correlation_50_global_equities_50'=>$row35,
        'sta_pb_bear_correlation_50_global_equities_50'=>$row36,
        'sta_pb_beta_msci_index'=>$row37,
        'sta_pb_beta_msci_index_last_3_year'=>$row38,
        'sta_pb_beta_msci_index_last_5_year'=>$row39,
        'sta_pb_beta_50_global_equities_50'=>$row40,
        'sta_pb_beta_50_global_equities_50_last_3_year'=>$row41,
        'sta_pb_beta_50_global_equities_50_last_5_year'=>$row42,
        'sta_pb_bull_beta_msci_index'=>$row43,
        'sta_pb_bear_beta_msci_index'=>$row44,
        'sta_pb_bull_beta_global_equities_50'=>$row45,
        'sta_pb_bear_beta_global_equities_50'=>$row46,
        'sta_pb_annual_sharpe_ratio'=>$row47,
        'sta_pb_annual_sharpe_ratio_last_3_year'=>$row48,
        'sta_pb_annual_sharpe_ratio_last_5_year'=>$row49,
        'sta_pb_downside_deviation_vs_0'=>$row50,
        'sta_pb_downside_deviation_vs_rf'=>$row51,
        'sta_pb_annual_treynor_ratio'=>$row52,
        'sta_pb_sortino_ratio_vs_0'=>$row53,
        'sta_pb_sortino_ratio_vs_rf'=>$row54,
        'sta_pb_sterling_ratio'=>$row55,
        'sta_pb_calmar_ratio'=>$row56,
        'sta_pb_hurst_index'=>$row57,
        'sta_pb_omega_0_00'=>$row58,
        'sta_pb_omega_0_50'=>$row59,
        'sta_pb_omega_1_00'=>$row60,
        'sta_pb_omega_1_50'=>$row61,
        'sta_pb_omega_2_00'=>$row62,
        'sta_pb_autocorrelated'=>$row63,
        'sta_pb_upmonth'=>$row64,
        'sta_pb_downmoth'=>$row65,
        'sta_pb_avg_return_during_bull_market'=>$row66,
        'sta_pb_avg_return_during_bear_market'=>$row67,
        'sta_pb_avg_return_during_bull_benchmark2'=>$row68,
        'sta_pb_avg_return_during_bear_benchmark2'=>$row69,
        'sta_pb_maximum_consecutive_gain'=>$row70,
        'sta_pb_maximum_consecutive_loss'=>$row71,
        'sta_pb_max_monthly_gain'=>$row72,
        'sta_pb_max_monthly_loss'=>$row73,
        'sta_pb_max_drawdown'=>$row74,
        'sta_pb_dateof_max_drawdown'=>$row75,
        'sta_pb_start_date_drawdown'=>$row76,
        'sta_pb_end_date_drawdown'=>$row77,
        'sta_pb_max_time_under_the_water_month'=>$row78,
        'sta_pb_max_possible_drawdown_99'=>$row79,
        'sta_pb_max_possible_drawdown_99_9'=>$row80,
        'sta_pb_max_time_under_the_water_month_99'=>$row81,
        'sta_pb_max_time_under_the_water_month_99_9'=>$row82,
        'sta_pb_monthly_portfolio_ret_attribution_may_20'=>$row83,
        'sta_pb_strategy'=>$row84,
        'sta_pb_inception_date'=>$row85,
        'sta_pb_quantitative_rating'=>$row86,
        'sta_pb_qualitative_rating'=>$row87,
        'sta_pb_final_rating'=>$row88,
        'sta_pb_last_return_date'=>$row89,
        'sta_pb_company_name'=>$row90,
        'sta_pb_manager_name'=>$row91,
        'sta_pb_address'=>$row92,
        'sta_pb_phone_number'=>$row93,
        'sta_pb_email'=>$row94,
        'sta_pb_asset_type'=>$row95,
        'sta_pb_proprietary_id'=>$row96,
        'sta_pb_country'=>$row97,
        'sta_pb_currency'=>$row98,
        'sta_pb_sub_strategy'=>$row99,
        'sta_pb_size'=>$row100,
        'sta_pb_status'=>$row101,
        'sta_pb_redemption_period'=>$row102,
        'sta_pb_redemption_period_2'=>$row103,
        'sta_pb_redemption_period_3'=>$row104,
        'sta_pb_notice_period'=>$row105,
        'sta_pb_performance_fee'=>$row106,
        'sta_pb_management_fee'=>$row107,
        'sta_pb_isin'=>$row108,
        'sta_pb_return_2015'=>$row109,
        'sta_pb_return_2016'=>$row110,
        'sta_pb_return_2017'=>$row111,
        'sta_pb_return_2018'=>$row112,
        'sta_pb_return_2019'=>$row113,
        'sta_pb_prime_broker'=>$row114,
        'sta_pb_return'=>$row115,
        'sta_pb_info_ratio_vs_msci_index'=>$row116,
        'sta_pb_info_ratio_vs_global_equities'=>$row117,
        'sta_pb_tracking_error_vs_msci_index'=>$row118,
        'sta_pb_tracking_error_vs_global_equities'=>$row119,
        'sta_pb_r2_vs_msci_index'=>$row120,
        'sta_pb_r2_vs_global_equities'=>$row121,
        'sta_pb_on_offshore'=>$row122,
        'sta_pb_quarter_to_date'=>$row123,
        'sta_pb_annual_return_sinceinception'=>$row124,
        'sta_pb_annual_bear_market_ret_vs_msci_index'=>$row125,
        'sta_pb_annual_bull_market_ret_vs_msci_index'=>$row126,
        'sta_pb_annual_bear_market_ret_vs_global_equities'=>$row127,
        'sta_pb_annual_bull_market_ret_vs_gloabal_equities'=>$row128,
        'sta_pb_avg_positive_return'=>$row129,
        'sta_pb_avg_negative_return'=>$row130,
        'sta_pb_outperforme_vs_msci_index_last_month'=>$row131,
        'sta_pb_outperforme_vs_global_equities_last_month'=>$row132,
        'sta_pb_annual_outperforme_vs_msci_index'=>$row133,
        'sta_pb_annual_outperforme_vs_global_equities'=>$row134,
        'sta_pb_rachev_ratio_99'=>$row135,
        'sta_pb_rachev_ratio_97'=>$row136,
        'sta_pb_rachev_ratio_95'=>$row137,
        'sta_pb_rachev_ratio_90'=>$row138,
        'sta_pb_annual_up_cap_vs_msci_index'=>$row139,
        'sta_pb_annual_up_cap_vs_gloabl_equities'=>$row140,
        'sta_pb_annual_down_cap_vs_msci_index'=>$row141,
        'sta_pb_annual_down_cap_vs_gloabl_equities'=>$row142,
        'sta_pb_annual_tracking_err_vs_msci_index_beta1'=>$row143,
        'sta_pb_annual_tracking_err_vs_gloabl_equities_beta1'=>$row144,
        'sta_pb_annual_jensen_alpha_vs_msci_index_vs_rf'=>$row145,
        'sta_pb_annual_jensen_alpha_vs_global_equities_vs_rf'=>$row146,
        'sta_pb_annual_info_ratio_vs_msci_index_beta1'=>$row147,
        'sta_pb_annual_info_ratio_vs_global_equities_beta1'=>$row148,
        'sta_pb_alpha_vs_msci_index'=>$row149,
        'sta_pb_alpha_vs_global_equities'=>$row150,
        'sta_pb_company_aum_m'=>$row151,
        'sta_pb_exponential_annual_volatility'=>$row152,
        'sta_pb_exponential_annual_volatility_last_12_month'=>$row153,
        'sta_pb_exponential_annual_volatility_last_3_year'=>$row154,
        'sta_pb_exponential_correlation_msci_index'=>$row155,
        'sta_pb_exponential_correlation_gloabl_equities'=>$row156,
        'sta_pb_exponential_annual_sharpe_ratio_vs_rf'=>$row157,
        'sta_pb_exponential_normal_monthly_var_99'=>$row158,
        'sta_pb_exponential_normal_monthly_var_97'=>$row159,
        'sta_pb_exponential_normal_monthly_var_95'=>$row160,
        'sta_pb_exponential_normal_monthly_var_90'=>$row161,
        'sta_pb_exponential_normal_weekly_var_99'=>$row162,
        'sta_pb_exponential_normal_weekly_var_97'=>$row163,
        'sta_pb_exponential_normal_weekly_var_95'=>$row164,
        'sta_pb_exponential_normal_weekly_var_90'=>$row165,
        'sta_pb_exponential_normal_daily_var_99'=>$row166,
        'sta_pb_exponential_normal_daily_var_97'=>$row167,
        'sta_pb_exponential_normal_daily_var_95'=>$row168,
        'sta_pb_exponential_normal_daily_var_90'=>$row169,
        'sta_pb_bias_ratio'=>$row170,
        'sta_pb_manger_bio'=>$row171,
        'sta_pb_min_investment'=>$row172,
        'sta_pb_auditor'=>$row173,
        'sta_pb_custodian'=>$row174,
        'sta_pb_administrator'=>$row175,
        'sta_pb_proxy1_start_date'=>$row176,
        'sta_pb_proxy1_end_date'=>$row177,
        'sta_pb_proxy2_start_date'=>$row178,
        'sta_pb_proxy2_end_date'=>$row179,
        'sta_pb_proxy3_start_date'=>$row180,
        'sta_pb_proxy3_end_date'=>$row181,
        'sta_pb_fund_domicile'=>$row182,
        'sta_pb_fund_contact'=>$row183,
        'sta_pb_fund_contact_email'=>$row184,
        'sta_pb_fund_contact_number'=>$row185,
        'sta_pb_city'=>$row186,
        'sta_pb_leverage'=>$row187,
        'sta_pb_lockup1_duration'=>$row188,
        'sta_pb_lockup2_duration'=>$row189,
        'sta_pb_lockup3_duration'=>$row190,
        'sta_pb_gate'=>$row191,
        'sta_pb_gate_frequency'=>$row192,
        'sta_pb_holdback_calendar2'=>$row193,
        'sta_pb_holdback_days2'=>$row194,
        'sta_pb_holdbak_paidafter2'=>$row195,
        'sta_pb_holdback_calendar3'=>$row196,
        'sta_pb_holdback_days3'=>$row197,
        'sta_pb_holdbak_paidafter3'=>$row198,
        'sta_pb_holdback_calendar4'=>$row199,
        'sta_pb_holdback_days4'=>$row200,
        'sta_pb_holdbak_paidafter4'=>$row201,
        'sta_pb_initial_payment'=>$row202,
        'sta_pb_lockup1_fee'=>$row203,
        'sta_pb_lockup2_fee'=>$row204,
        'sta_pb_lockup3_fee'=>$row205,
        'sta_pb_lockup1_type'=>$row206,
        'sta_pb_lockup2_type'=>$row207,
        'sta_pb_lockup3_type'=>$row208,
        'sta_pb_redemption_calendar1'=>$row209,
        'sta_pb_redemption_calendar2'=>$row210,
        'sta_pb_redemption_calendar3'=>$row211,
        'sta_pb_redemption_fee1'=>$row212,
        'sta_pb_redemption_fee2'=>$row213,
        'sta_pb_redemption_fee3'=>$row214,
        'sta_pb_redemption_frequency1'=>$row215,
        'sta_pb_redemption_frequency2'=>$row216,
        'sta_pb_redemption_frequency3'=>$row217,
        'sta_pb_subscription_calendar'=>$row218,
        'sta_pb_second_max_drawdown'=>$row219,
        'sta_pb_date_second_max_drawdown'=>$row220,
        'sta_pb_portfolio_id'=>$portfolio_id,
        'sta_pb_client_id'=>$portfolio_user,
        'sta_pb_addedon'=>Carbon::now(),
        'sta_pb_addedby'=>1,
        'sta_pb_uddatedon'=>Carbon::now(),
        'sta_pb_updatedby'=>1]
     ]);
    }
   
        }
        if($Index == 7)
        {
             if ( $row[0] != '') {
            
             $data=DB::table('lc_statistics_a')->insert([
    ['sta_a_client_id' => $portfolio_user,'sta_a_portfolio_id' => $portfolio_id,'sta_a_assets'=>$row0,'sta_a_weight'=>$row1,'sta_a_historical_annual_assets_inception'=>$row2,'sta_a_historical_annual_time_window'=>$row3,'sta_a_equilibrium_annual_mean'=>$row4,'sta_a_annual_volatility_assets_inception'=>$row5,'sta_a_annual_volatility_time_window'=>$row6,'sta_a_skewness'=>$row7,'sta_a_excess_kurtosis'=>$row8,'sta_a_last_1_month_return'=>$row9,'sta_a_last_3_months_return'=>$row10,'sta_a_last_12_months_return'=>$row11,'sta_a_year-to-date_return'=>$row12,'sta_a_annual_last_3_months_return'=>$row13,'sta_a_annual_last_3_years_return'=>$row14,'sta_a_annual_last_5_years_return'=>$row15,'sta_a_annual_volatility_last_12_months'=>$row16,'sta_a_annual_volatility_last_3_years'=>$row17,'sta_a_annual_volatility_last_5_years'=>$row18,'sta_a_normal_monthly_var_99'=>$row19,'sta_a_modified_monthly_var_99'=>$row20,'sta_a_conditional_monthly_var_99'=>$row21,'sta_a_normal_monthly_var_97'=>$row22,'sta_a_modified_monthly_var_97'=>$row23,'sta_a_conditional_monthly_var_97'=>$row24,'sta_a_normal_monthly_var_95'=>$row25,'sta_a_modified_monthly_var_95'=>$row26,'sta_a_conditional_monthly_var_95'=>$row27,'sta_a_correlation_MSCI_ACWI'=>$row28,'sta_a_correlation_global_equities_fiftyig'=>$row29,'sta_a_bull_correlation_MSCI_ACWI'=>$row30,'sta_a_bear_correlation_MSCI_ACWI'=>$row31,'sta_a_bull_correlation_global_equities_fiftyig'=>$row32,'sta_a_bear_correlation_global_equities_fiftyig'=>$row33,'sta_a_beta_MSCI_ACWI'=>$row34,'sta_a_beta_MSCI_ACWI_three_years'=>$row35,'sta_a_beta_MSCI_ACWI_five_years'=>$row36,'sta_a_beta_global_equities_fiftyig'=>$row37,'sta_a_beta_global_equities_fiftyig_three_years'=>$row38,'sta_a_beta_global_equities_fiftyig_five_years'=>$row39,'sta_a_bull_beta_MSCI_ACWI'=>$row40,'sta_a_bear_beta_MSCI_ACWI'=>$row41,'sta_a_bull_beta_global_equities_fiftyig'=>$row42,'sta_a_bear_beta_global_equities_fiftyig'=>$row43,'sta_a_annual_sharpe_ratio'=>$row44,'sta_a_annual_sharpe_ratio_three_years'=>$row45,'sta_a_annual_sharpe_ratio_five_years'=>$row46,'sta_a_downside_deviation_0'=>$row47,'sta_a_downside_deviation_rf'=>$row48,'sta_a_annual_treynor_ratio'=>$row49,'sta_a_sortino_ratio_0'=>$row50,'sta_a_sortino_ratio_rf'=>$row51,'sta_a_sterling_ratio'=>$row52,'sta_a_calmar_ratio'=>$row53,'sta_a_hurst_index'=>$row54,'sta_a_omega_0_00'=>$row55,'sta_a_omega_0_50'=>$row56,'sta_a_omega_1_00'=>$row57,'sta_a_omega_1_50'=>$row58,'sta_a_omega_2_00'=>$row59,'sta_a_autocorrelated'=>$row60,'sta_a_up_months'=>$row61,'sta_a_down_months'=>$row62,'sta_a_average_bull_market'=>$row63,'sta_a_average_bear_market'=>$row64,'sta_a_average_bull_benchmark2'=>$row65,'sta_a_average_bear_benchmark2'=>$row66,'sta_a_maximum_consecutive_gain'=>$row67,'sta_a_maximum_consecutive_loss'=>$row68,'sta_a_max_monthly_gain'=>$row69,'sta_a_max_monthly_loss'=>$row70,'sta_a_max_drawdown'=>$row71,'sta_a_date_max_drawdown'=>$row72,'sta_a_startdate_drawdown'=>$row73,'sta_a_enddate_drawdown'=>$row74,'sta_a_max_time_under_the_water'=>$row75,'sta_a_max_possible_drawdown_99'=>$row76,'sta_a_max_possible_drawdown_99_9'=>$row77,'sta_a_max_possible_time_under_the_water_99'=>$row78,'sta_a_max_possible_time_under_the_water_99_9'=>$row79,'sta_a_monthly_portfolio_may_20'=>$row80,'sta_a_strategy'=>$row81,'sta_a_inception_date'=>$row82,'sta_a_quantitative_rating'=>$row83,'sta_a_qualitative_rating'=>$row84,'sta_a_final_rating'=>$row85,'sta_a_last_return_date'=>$row86,'sta_a_company_name'=>$row87,'sta_a_manager_name'=>$row88,'sta_a_address'=>$row89,'sta_a_phone_number'=>$row90,'sta_a_email'=>$row91,'sta_a_asset_type'=>$row92,'sta_a_proprietary_id'=>$row93,'sta_a_country'=>$row94,'sta_a_currency'=>$row95,'sta_a_sub_strategy'=>$row96,'sta_a_size_m'=>$row97,'sta_a_status'=>$row98,'sta_a_redemption_period'=>$row99,'sta_a_redemption_period2'=>$row100,'sta_a_redemption_period3'=>$row101,'sta_a_notice_period'=>$row102,'sta_a_performance_fee'=>$row103,'sta_a_management_fee'=>$row104,'sta_a_isin'=>$row105,'sta_a_return_2015'=>$row106,'sta_a_return_2016'=>$row107,'sta_a_return_2017'=>$row108,'sta_a_return_2018'=>$row109,'sta_a_return_2019'=>$row110,'sta_a_prime_broker'=>$row111,'sta_a_return'=>$row112,'sta_a_information_ratio_MSCI_ACWI'=>$row113,'sta_a_information_ratio_global_equities_fiftyig'=>$row114,'sta_a_tracking_error_MSCI_ACWI'=>$row115,'sta_a_tracking_error_global_equities_fiftyig'=>$row116,'sta_a_R2_MSCI_ACWI'=>$row117,'sta_a_R2_global_equities_fiftyig'=>$row118,'sta_a_on_offshore'=>$row119,'sta_a_quater_to_date'=>$row120,'sta_a_annual_sinceinception'=>$row121,'sta_a_annual_bear_market_MSCI_ACWI'=>$row122,'sta_a_annual_bull_market_MSCI_ACWI'=>$row123,'sta_a_annual_bear_market_global_equities_fiftyig'=>$row124,'sta_a_annual_bull_market_global_equities_fiftyig'=>$row125,'sta_a_average_positive_return'=>$row126,'sta_a_average_negative_return'=>$row127,'sta_a_outperformance_ MSCI_ACWI_index_last_month'=>$row128,'sta_a_outperformance_global_equities_fiftyig_last_month'=>$row129,'sta_a_annual_outperformance_MSCI_ACWI'=>$row130,'sta_a_annual_outperformance_global_equities_fiftyig'=>$row131,'sta_a_rachev_ratio_99'=>$row132,'sta_a_rachev_ratio_97'=>$row133,'sta_a_rachev_ratio_95'=>$row134,'sta_a_rachev_ratio_90'=>$row135,'sta_a_annual_up_capture_MSCI_ACWI'=>$row136,'sta_a_annual_up_capture_global_equities_fiftyig'=>$row137,'sta_a_annual_down_capture_MSCI_ACWI'=>$row138,'sta_a_annual_down_capture_global_equities_fiftyig'=>$row139,'sta_a_annual_tracking_error_MSCI_ACWI'=>$row140,'sta_a_annual_tracking_error_global_equities_fiftyig'=>$row141,'sta_a_annual_jensen_alpha_MSCI_ACWI'=>$row142,'sta_a_annual_jensen_alpha_global_equities_fiftyig'=>$row143,'sta_a_annual_information_ratio_MSCI_ACWI'=>$row144,'sta_a_annual_information_ratio_global_equities_fiftyig'=>$row145,'sta_a_alpha_MSCI_ACWI'=>$row146,'sta_a_alpha_global_equities_fiftyig'=>$row147,'sta_a_alpha_global_equities_fiftyig'=>$row148,'sta_a_exponential_annual_volatility'=>$row149,'sta_a_exponential_annual_volatility_12months'=>$row150,'sta_a_exponential_annual_volatility_3years'=>$row151,'sta_a_exponential_correlation_MSCI_ACWI'=>$row152,'sta_a_exponential_correlation_global_equities_fiftyig'=>$row153,'sta_a_exponential_annual_sharpe_ratio'=>$row154,'sta_a_exponential_normal_monthly_var_99'=>$row155,'sta_a_exponential_normal_monthly_var_97'=>$row156,'sta_a_exponential_normal_monthly_var_95'=>$row157,'sta_a_exponential_normal_monthly_var_90'=>$row158,'sta_a_exponential_normal_weekly_var_99'=>$row159,'sta_a_exponential_normal_weekly_var_97'=>$row160,'sta_a_exponential_normal_weekly_var_95'=>$row161,'sta_a_exponential_normal_weekly_var_90'=>$row162,'sta_a_exponential_normal_daily_var_99'=>$row163,'sta_a_exponential_normal_daily_var_97'=>$row164,'sta_a_exponential_normal_daily_var_95'=>$row165,'sta_a_exponential_normal_daily_var_90'=>$row166,'sta_a_bias_ratio'=>$row167,'sta_a_manager_biography'=>$row168,'sta_a_minimum_investment'=>$row169,'sta_a_auditor'=>$row170,'sta_a_custodian'=>$row171,'sta_a_administrator'=>$row172,'sta_a_proxy_1_start_date'=>$row173,'sta_a_proxy_1_end_date'=>$row174,'sta_a_proxy_2_start_date'=>$row175,'sta_a_proxy_2_end_date'=>$row176,'sta_a_proxy_3_start_date'=>$row177,'sta_a_proxy_3_end_date'=>$row178,'sta_a_fund_domicile'=>$row179,'sta_a_fund_contact'=>$row180,'sta_a_fund_contact_email'=>$row181,'sta_a_fund_contact_number'=>$row182,'sta_a_city'=>$row183,'sta_a_leverage'=>$row184,'sta_a_lockup_1_duration'=>$row185,'sta_a_lockup_2_duration'=>$row186,'sta_a_lockup_3_duration'=>$row187,'sta_a_gate'=>$row188,'sta_a_gate_frequency'=>$row189,'sta_a_holdback_calendar_2'=>$row190,'sta_a_holdback_days_2'=>$row191,'sta_a_holdback_paidafter_2'=>$row192,'sta_a_holdback_calendar_3'=>$row193,'sta_a_holdback_days_3'=>$row194,'sta_a_holdback_paidafter_3'=>$row195,'sta_a_holdback_calendar_4'=>$row196,'sta_a_holdback_days_4'=>$row197,'sta_a_holdback_paidafter_4'=>$row198,'sta_a_initial_payment'=>$row199,'sta_a_lockup_1_fee'=>$row200,'sta_a_lockup_2_fee'=>$row201,'sta_a_lockup_3_fee'=>$row202,'sta_a_lockup_1_type'=>$row203,'sta_a_lockup_2_type'=>$row204,'sta_a_lockup_3_type'=>$row205,'sta_a_redemption_calendar_1'=>$row206,'sta_a_redemption_calendar_2'=>$row207,'sta_a_redemption_calendar_3'=>$row208,'sta_a_redemption_fee_1'=>$row209,'sta_a_redemption_fee_2'=>$row210,'sta_a_redemption_fee_3'=>$row211,'sta_a_redemption_frequency_1'=>$row212,'sta_a_redemption_frequency_2'=>$row213,'sta_a_redemption_frequency_3'=>$row214,'sta_a_subscription_calendar'=>$row215,'sta_a_second_max_drawdown'=>$row216,'sta_a_date_second_max_drawdown'=>$row217,'sta_a_bloomberg_ticker'=>$row218,'sta_a_bond_rank'=>$row219,'sta_a_currentprice'=>$row220,'sta_a_lc_target_price'=>$row221,'sta_a_coupon'=>$row222,'sta_a_yield_worst'=>$row223,'sta_a_effective_duration'=>$row224,'sta_a_maturity_date'=>$row225,'sta_a_call_date'=>$row226,'sta_a_bloomberg_composite_ranking'=>$row227,'sta_a_investment_geography'=>$row228,'sta_a_industry_focus'=>$row229,'sta_a_vice_stock_indicator'=>$row230,'sta_a_addedon'=>Carbon::now(),'sta_a_addedby'=>1,'sta_a_updatedon'=>Carbon::now(),'sta_a_updatedby'=>1]
   ]);
    }
   
        }
        if($Index == 8)
        {
           if ( $row[0] != '') {
           $data=DB::table('lc_stress_test_negative')->insert([
    ['stn_client_id' => $portfolio_user,'stn_portfolio_id' => $portfolio_id,'stn_rank'=>$row0,'stn_bear_market'=>$row1,'stn_portfolio_bear_market'=>$row2,'stn_dates'=>$row3,'stn_benchmark'=>$row4,'stn_addedon'=>Carbon::now(),'stn_addedby'=>1,'stn_updatedon'=>Carbon::now(),'stn_updatedby'=>1]
     ]);
    }
   
        }
        if($Index == 9)
        {
       if ( $row[0] != '') {
           $data=DB::table('lc_stress_test_positive')->insert([
    ['stp_client_id' => $portfolio_user,'stp_portfolio_id' => $portfolio_id,'stp_rank'=>$row0,'stp_bear_market'=>$row1,'stp_portfolio_bear_market'=>$row2,'stp_dates'=>$row3,'stp_benchmark'=>$row4,'stp_addedon'=>Carbon::now(),'stp_addedby'=>1,'stp_updatedon'=>Carbon::now(),'stp_updatedby'=>1]
]);
    }
     
        }
        if($Index == 10)
        {
        if ( $row[0] != '') {
           $data=DB::table('lc_drawdown')->insert([
         ['drawdown_client_id' => $portfolio_user,'drawdown_portfolio_id' => $portfolio_id,'drawdown_date'=>$row0,'drawdown_cumulative'=>$row1,'drawdown_drawdown'=>$row2,'drawdown_msciindex'=>$row3,'drawdown_globalequities'=>$row4,'drawdown_addedon'=>Carbon::now(),'drawdown_addedby'=>1,'drawdown_updatedon'=>Carbon::now(),'drawdown_updatedby'=>1]
   ]);
    } 
       
        }
        if($Index == 11)
        {
        if ( $row[0] != '') {
         
           $data=DB::table('lc_crisis')->insert([
    ['crisis_portfolio_id' => $portfolio_id,'crisis_client_id' => $portfolio_user,'crisis_startdate'=>$row0,'crisis_lastreturn'=>$row1,'crisis_crisis'=>$row2,'crisis_portfolioreturn'=>$row3,'crisis_benchmark1'=>$row4,'crisis_benchmark2'=>$row5,'crisis_addedon'=>Carbon::now(),'crisis_addedby'=>1,'crisis_updatedon'=>Carbon::now(),'crisis_updatedby'=>1]
    ]);
    }
   }
  }
       $status = 1;
     }
   }
    //  $destinationPath = public_path('output/archive/'.$filename);
    // $move = File::move($path, $destinationPath);

      DB::table('lc_altsoft_job_queue')->where('id',$is_file->id )->update(
       ['excel_status' => "1"]);

       $output_excel_path = 'file://10.10.0.7/Navigator/Output/Archive/'.$filename;
       DB::table('lc_portfolio')->where('portfolio_id','=',$is_file->portfolio_id)->update([
       'portfolio_status' => "success",
       'portfolio_previous_status' => "success",
       'portfolio_output_excel'=>$output_excel_path
       ]);

      DB::commit();
  }
   else
   {
    echo "no files found";
   }
  }
  public function delete(){
        DB::table('lc_client')->truncate();
        DB::table('lc_portfolio')->truncate();
        DB::table('lc_crisis')->truncate();
        DB::table('lc_cumulative_returns_a')->truncate();
        DB::table('lc_cumulative_returns_pb')->truncate();
        DB::table('lc_drawdown')->truncate();
        DB::table('lc_monthlyreturns')->truncate();
        DB::table('lc_pba_summary')->truncate();
        DB::table('lc_returns_a')->truncate();
        DB::table('lc_returns_pb')->truncate();
        DB::table('lc_statistics_a')->truncate();
        DB::table('lc_statistics_pb')->truncate();
        DB::table('lc_stress_test_negative')->truncate();
        DB::table('lc_stress_test_positive')->truncate();
        DB::table('lc_eventdetails')->truncate();
        DB::table('lc_regionaldetails')->truncate();
        DB::table('lc_altsoft_job_queue')->truncate();

        return response()->json([
        "status"  => "Success",
        "data" => "Table Truncated" 
      ]);
   }

}
