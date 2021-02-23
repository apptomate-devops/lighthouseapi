<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use DB;
use Carbon\Carbon;

class XmlController extends Controller
{
    public function create()
    {
    $date=Carbon::now();
    $eventcount=DB::table('lc_portfolio')->whereDate('portfolio_createdon', '=', Carbon::today()->toDateString())->count();
    $nextevent = $eventcount + 1;
    $eventid="lc" .str_pad($date->day, 2, "0", STR_PAD_LEFT).str_pad($date->month, 2, "0", STR_PAD_LEFT).substr( $date->year, -2).str_pad($nextevent, '5', '0', STR_PAD_LEFT);

    $data = DB::table('lc_portfolio')->where('portfolio_xml_status',0)->first();
         
        $dom = new \DOMDocument();

        $dom->encoding = 'utf-8';

        $dom->xmlVersion = '1.0';

        $dom->formatOutput = true;

        $root = $dom->createElement('ASPackage');

        $head_node = $dom->createElement('Head');

        $head_child_package = $dom->createElement('PackageName','AS Export');

        $head_child_createddate = $dom->createElement('CreatedDate',Carbon::now()->format('Y-m-d'));

        $head_child_schemaversion = $dom->createElement('SchemaVersion','976');

        $head_child_asversion = $dom->createElement('AsVersion','9.3.2.48279');

        $head_node->appendChild($head_child_package);

        $head_node->appendChild($head_child_createddate);

        $head_node->appendChild($head_child_schemaversion);

        $head_node->appendChild($head_child_asversion);

        $body_node = $dom->createElement('Body');

        $portfolios_node = $dom->createElement('Portfolios');

        $portfolio_node = $dom->createElement('Portfolio');

        $portfolio_child_id = $dom->createElement('ID',$eventid);

        $portfolio_child_country = $dom->createElement('Country','[Not Classified]');

        $portfolio_child_currency = $dom->createElement('Currency',$data->portfolio_currency);

        $portfolio_child_performancefee = $dom->createElement('PerformanceFee','0,005');

        $portfolio_child_managementfee = $dom->createElement('ManagementFee','0,005');

        $portfolio_child_riskfreeconstant = $dom->createElement('RiskFreeConstant','0,005');

        $portfolio_child_otherFees = $dom->createElement('OtherFees','0');

        $portfolio_child_name = $dom->createElement('Name','Optimization Universe');

        $portfolio_child_Timewindow = $dom->createElement('TimeWindow');

        $TimeWindow_from = new \DOMAttr('from', '2006-11-30');
        
        $portfolio_child_Timewindow->setAttributeNode($TimeWindow_from);

        $TimeWindow_to = new \DOMAttr('to','2020-01-31');

        $portfolio_child_Timewindow->setAttributeNode($TimeWindow_to);

        $portfolio_node->appendChild($portfolio_child_id);

        $portfolio_node->appendChild($portfolio_child_country);

        $portfolio_node->appendChild($portfolio_child_currency);

        $portfolio_node->appendChild($portfolio_child_performancefee);

        $portfolio_node->appendChild($portfolio_child_managementfee);

        $portfolio_node->appendChild($portfolio_child_riskfreeconstant);
        
        $portfolio_node->appendChild($portfolio_child_otherFees);

        $portfolio_node->appendChild($portfolio_child_name);

        $portfolio_node->appendChild($portfolio_child_Timewindow);

        $options_node = $dom->createElement('Options');

        $option_managementFee_node = $dom->createElement('ManagementFee','0,005');

        $option_performanceFee_node = $dom->createElement('PerformanceFee','0,005');

        $option_riskfree_node = $dom->createElement('RiskFree','0,0025');

        $option_frequency = $dom->createElement('Frequency','M');
     
        if($data->portfolio_coreunit == "%")
        {
         $aum = $data->portfolio_core * $data->portfolio_clientequity;
        }
        else
        {
         $aum = $data->portfolio_core;
        }

        $option_AUM = $dom->createElement('AUM',$aum);

        $option_weeklyreturnsday = $dom->createElement('WeeklyReturnsDay','Friday');

        $option_techniqueoptimize = $dom->createElement('TechniqueOptimize','Single Point');

        $option_riskmeasureoptimize = $dom->createElement('RiskMeasureOptimize','Modified VaR 99%');

        // $option_leverage = $dom->createElement('Leverage');

        // $leverage_constantannually = $dom->createElement('ConstantAnnually','4');

        // $Constantannually_attr = new \DOMAttr('Active','true');

        // $leverage_constantannually->setAttributeNode($Constantannually_attr);

        // $leverage_costasset = $dom->createElement('CostAsset');

        // $costasset_asset = $dom->createElement('Asset');

        // $asset_name = $dom->createElement('Name',$data->portfolio_costofborrowing_benchmark);

        // $costasset_asset->appendChild($asset_name);

        // $leverage_costasset->appendChild($costasset_asset);

        // $annualbps_node = $dom->createElement('AnnualBps',$data->portfolio_costofborrowing);

        // $option_leverage->appendChild($leverage_constantannually);

        // $option_leverage->appendChild($leverage_costasset);

        // $option_leverage->appendChild($annualbps_node);

        $options_node->appendChild($option_managementFee_node);

        $options_node->appendChild($option_performanceFee_node);

        $options_node->appendChild($option_riskfree_node);

        $options_node->appendChild($option_frequency);

        $options_node->appendChild($option_AUM);

        $options_node->appendChild($option_weeklyreturnsday);

        $options_node->appendChild($option_techniqueoptimize);

        $options_node->appendChild($option_riskmeasureoptimize);

       // $options_node->appendChild($option_leverage);

        $portfolio_node->appendChild($options_node);

        $benchmark1_node = $dom->createElement('Benchmark1');

        $benchmark1_child_name = $dom->createElement('Name','S&amp;P 500 Index');

        $benchmark1_node->appendChild($benchmark1_child_name);

        $portfolio_node->appendChild($benchmark1_node);

        $benchmark2_node = $dom->createElement('Benchmark2');

        $benchmark2_child_name = $dom->createElement('Name','S&amp;P 500 Index');

        $benchmark2_node->appendChild($benchmark2_child_name);

        $portfolio_node->appendChild($benchmark2_node);

        $constraints_node = $dom->createElement('Constraints');
        
        $assetsweights_node = $dom->createElement('AssetsWeights');

        $assetsweights_node_attr = new \DOMAttr('IsChecked','true');

        $assetsweights_node->setAttributeNode($assetsweights_node_attr);

        $assetweights_child_values = $dom->createElement('Values');
        

        $asset_record = DB::table('lc_asset')->get();

        foreach($asset_record as $asset_data)
        {
        $assetweights_child_values_value1 = $dom->createElement('Value','0');

        $assetweights_child_values_value1_attr_name = new \DOMAttr('Name',$asset_data->asset_fundname);

        $assetweights_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $assetweights_child_values_value1_attr_max = new \DOMAttr('Max','1');

        $assetweights_child_values_value1->setAttributeNode($assetweights_child_values_value1_attr_name);

        $assetweights_child_values_value1->setAttributeNode($assetweights_child_values_value1_attr_min);

        $assetweights_child_values_value1->setAttributeNode($assetweights_child_values_value1_attr_max); 

        $assetweights_child_values->appendChild($assetweights_child_values_value1);  

        }
      
        $assetsweights_node->appendChild($assetweights_child_values);
        
        $constraints_node->appendChild($assetsweights_node);



        $expannreturn_node = $dom->createElement('ExpAnnReturn');

        $expannreturn_node_attr = new \DOMAttr('IsChecked','true');

        $expannreturn_node->setAttributeNode($expannreturn_node_attr);

        $expannreturn_child_values = $dom->createElement('Values');
        

        $asset_record = DB::table('lc_asset')->get();

        foreach($asset_record as $asset_data)
        {
        $expannreturn_child_values_value1 = $dom->createElement('Value','0');

        $expannreturn_child_values_value1_attr_name = new \DOMAttr('Name',$asset_data->asset_fundname);

        $expannreturn_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $expannreturn_child_values_value1_attr_max = new \DOMAttr('Max','0');

        $expannreturn_child_values_value1->setAttributeNode($expannreturn_child_values_value1_attr_name);

        $expannreturn_child_values_value1->setAttributeNode($expannreturn_child_values_value1_attr_min);

        $expannreturn_child_values_value1->setAttributeNode($expannreturn_child_values_value1_attr_max); 

        $expannreturn_child_values->appendChild($expannreturn_child_values_value1);  

        }
      
        $expannreturn_node->appendChild($expannreturn_child_values);
        
        $constraints_node->appendChild($expannreturn_node);



        $expannvolatity_node = $dom->createElement('ExpAnnVolatility');

        $expannvolatity_node_attr = new \DOMAttr('IsChecked','true');

        $expannvolatity_node->setAttributeNode($expannvolatity_node_attr);

        $expannvolatity_child_values = $dom->createElement('Values');
        

        $asset_record = DB::table('lc_asset')->get();

        foreach($asset_record as $asset_data)
        {
        $expannvolatity_child_values_value1 = $dom->createElement('Value','0');

        $expannvolatity_child_values_value1_attr_name = new \DOMAttr('Name',$asset_data->asset_fundname);

        $expannvolatity_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $expannvolatity_child_values_value1_attr_max = new \DOMAttr('Max','0');

        $expannvolatity_child_values_value1->setAttributeNode($expannvolatity_child_values_value1_attr_name);

        $expannvolatity_child_values_value1->setAttributeNode($expannvolatity_child_values_value1_attr_min);

        $expannvolatity_child_values_value1->setAttributeNode($expannvolatity_child_values_value1_attr_max); 

        $expannvolatity_child_values->appendChild($expannvolatity_child_values_value1);  

        }
      
        $expannvolatity_node->appendChild($expannvolatity_child_values);
        
        $constraints_node->appendChild($expannvolatity_node);





        $strategyweights_node = $dom->createElement('StrategyWeights');

        $strategyweights_node_attr = new \DOMAttr('IsChecked','true');

        $strategyweights_node->setAttributeNode($strategyweights_node_attr);

        $strategyweights_child_values = $dom->createElement('Values');

        $strategyweights_child_values_value1 = $dom->createElement('Value');

        $strategyweights_child_values_value1_attr_name = new \DOMAttr('Name','Statistical Arbitrage');


        if($data->portfolio_fixedincome_status == "1")
        {
         $strategyweights_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','true');

         $strategyweights_child_values_value1_attr_min = new \DOMAttr('Min',$data->portfolio_fixedincome_directholding_min/100);

         $strategyweights_child_values_value1_attr_max = new \DOMAttr('Max',$data->portfolio_fixedincome_directholding_max/100);
        }
        else
        {
         $strategyweights_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','false');

         $strategyweights_child_values_value1_attr_min = new \DOMAttr('Min','0');

         $strategyweights_child_values_value1_attr_max = new \DOMAttr('Max','1');
        }

        $strategyweights_child_values_value1->setAttributeNode($strategyweights_child_values_value1_attr_name);

        $strategyweights_child_values_value1->setAttributeNode($strategyweights_child_values_value1_attr_ischecked);

        $strategyweights_child_values_value1->setAttributeNode($strategyweights_child_values_value1_attr_min);

        $strategyweights_child_values_value1->setAttributeNode($strategyweights_child_values_value1_attr_max); 

        $strategyweights_child_values->appendChild($strategyweights_child_values_value1);  

        $strategyweights_child_values_value2 = $dom->createElement('Value');

        $strategyweights_child_values_value2_attr_name = new \DOMAttr('Name','Equity Hedge');

        if($data->portfolio_equities_status == "1")
        {

        $strategyweights_child_values_value2_attr_ischecked = new \DOMAttr('IsChecked','true');

        $strategyweights_child_values_value2_attr_min = new \DOMAttr('Min',$data->portfolio_equities_directholding_min/100);

        $strategyweights_child_values_value2_attr_max = new \DOMAttr('Max',$data->portfolio_equities_directholding_max/100);
        }
        else
        {
         $strategyweights_child_values_value2_attr_ischecked = new \DOMAttr('IsChecked','false');

        $strategyweights_child_values_value2_attr_min = new \DOMAttr('Min','0');

        $strategyweights_child_values_value2_attr_max = new \DOMAttr('Max','1');    
        }


        $strategyweights_child_values_value2->setAttributeNode($strategyweights_child_values_value2_attr_name);

        $strategyweights_child_values_value1->setAttributeNode($strategyweights_child_values_value2_attr_ischecked);

        $strategyweights_child_values_value2->setAttributeNode($strategyweights_child_values_value2_attr_min);

        $strategyweights_child_values_value2->setAttributeNode($strategyweights_child_values_value2_attr_max); 

        $strategyweights_child_values->appendChild($strategyweights_child_values_value2);  

        $strategyweights_child_values_value3 = $dom->createElement('Value');

        $strategyweights_child_values_value3_attr_name = new \DOMAttr('Name','[Not Classified]');

        if($data->portfolio_alternatives_status == "1")
        {
        $strategyweights_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','true');

        $strategyweights_child_values_value3_attr_min = new \DOMAttr('Min',$data->portfolio_alternative_directholdings_min/100);

        $strategyweights_child_values_value3_attr_max = new \DOMAttr('Max',$data->portfolio_alternative_directholdings_max/100);
        }
        else
        {
         $strategyweights_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','false');

        $strategyweights_child_values_value3_attr_min = new \DOMAttr('Min','0');

        $strategyweights_child_values_value3_attr_max = new \DOMAttr('Max','1');        
        }



        $strategyweights_child_values_value3->setAttributeNode($strategyweights_child_values_value3_attr_name);

        $strategyweights_child_values_value3->setAttributeNode($strategyweights_child_values_value3_attr_ischecked);

        $strategyweights_child_values_value3->setAttributeNode($strategyweights_child_values_value3_attr_min);

        $strategyweights_child_values_value3->setAttributeNode($strategyweights_child_values_value3_attr_max);

        $strategyweights_child_values->appendChild($strategyweights_child_values_value3);  

        $strategyweights_node->appendChild($strategyweights_child_values);

        $constraints_node->appendChild($strategyweights_node);

        $assettypeweights_node = $dom->createElement('AssetTypeWeights');

        $assettypeweights_node_attr = new \DOMAttr('IsChecked','true');

        $assettypeweights_node->setAttributeNode($assettypeweights_node_attr);

        $assettypeweights_child_values = $dom->createElement('Values');

        $assettypeweights_child_values_value1 = $dom->createElement('Value');

        $assettypeweights_child_values_value1_attr_name = new \DOMAttr('Name','Hedge Fund');

        if($data->portfolio_cash_status == "1")
        {
        $assettypeweights_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','true');

        $assettypeweights_child_values_value1_attr_min = new \DOMAttr('Min',$data->portfolio_cash_min/100);

        $assettypeweights_child_values_value1_attr_max = new \DOMAttr('Max',$data->portfolio_cash_max/100);
        }
        else
        {
        $assettypeweights_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','false');

        $assettypeweights_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $assettypeweights_child_values_value1_attr_max = new \DOMAttr('Max','1');
        }

   

        $assettypeweights_child_values_value1->setAttributeNode($assettypeweights_child_values_value1_attr_name);

        $assettypeweights_child_values_value1->setAttributeNode($assettypeweights_child_values_value1_attr_ischecked);

        $assettypeweights_child_values_value1->setAttributeNode($assettypeweights_child_values_value1_attr_min);

        $assettypeweights_child_values_value1->setAttributeNode($assettypeweights_child_values_value1_attr_max); 

        $assettypeweights_child_values->appendChild($assettypeweights_child_values_value1);  

        $assettypeweights_child_values_value2 = $dom->createElement('Value');

        $assettypeweights_child_values_value2_attr_name = new \DOMAttr('Name','Equity');

        if($data->portfolio_equities_status == "1")
        {
        $assettypeweights_child_values_value2_attr_ischecked = new \DOMAttr('IsChecked','true');

        $assettypeweights_child_values_value2_attr_min = new \DOMAttr('Min',($data->portfolio_equities_min/100)*($data->portfolio_equities_directholding_min/100));

        $assettypeweights_child_values_value2_attr_max = new \DOMAttr('Max',($data->portfolio_equities_max/100)*($data->portfolio_equities_directholding_max/100));

        }
        else
        {
        $assettypeweights_child_values_value2_attr_ischecked = new \DOMAttr('IsChecked','false');

        $assettypeweights_child_values_value2_attr_min = new \DOMAttr('Min','0');

        $assettypeweights_child_values_value2_attr_max = new \DOMAttr('Max','1');   
        }


        $assettypeweights_child_values_value2->setAttributeNode($assettypeweights_child_values_value2_attr_name);

        $assettypeweights_child_values_value1->setAttributeNode($assettypeweights_child_values_value2_attr_ischecked);

        $assettypeweights_child_values_value2->setAttributeNode($assettypeweights_child_values_value2_attr_min);

        $assettypeweights_child_values_value2->setAttributeNode($assettypeweights_child_values_value2_attr_max); 

        $assettypeweights_child_values->appendChild($assettypeweights_child_values_value2);  

        $assettypeweights_child_values_value3 = $dom->createElement('Value');

        $assettypeweights_child_values_value3_attr_name = new \DOMAttr('Name','Fixed Income');

        if($data->portfolio_fixedincome_status == "1")
        {

        $assettypeweights_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','true');

        $assettypeweights_child_values_value3_attr_min = new \DOMAttr('Min',($data->portfolio_fixedincome_min/100)*($data->portfolio_fixedincome_directholding_min/100));

        $assettypeweights_child_values_value3_attr_max = new \DOMAttr('Max',($data->portfolio_fixedincome_max/100)*($data->portfolio_fixedincome_directholding_min/100));
        }
        else
        {
        $assettypeweights_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','false');

        $assettypeweights_child_values_value3_attr_min = new \DOMAttr('Min','0');

        $assettypeweights_child_values_value3_attr_max = new \DOMAttr('Max','1');           
        }


        $assettypeweights_child_values_value3->setAttributeNode($assettypeweights_child_values_value3_attr_name);

        $assettypeweights_child_values_value3->setAttributeNode($assettypeweights_child_values_value3_attr_ischecked);

        $assettypeweights_child_values_value3->setAttributeNode($assettypeweights_child_values_value3_attr_min);

        $assettypeweights_child_values_value3->setAttributeNode($assettypeweights_child_values_value3_attr_max);

        $assettypeweights_child_values->appendChild($assettypeweights_child_values_value3);  

        $assettypeweights_child_values_value4 = $dom->createElement('Value');

        $assettypeweights_child_values_value4_attr_name = new \DOMAttr('Name','Trading Tools');

        if($data->portfolio_alternatives_status == "1")
        {
        $assettypeweights_child_values_value4_attr_ischecked = new \DOMAttr('IsChecked','true');

        $assettypeweights_child_values_value4_attr_min = new \DOMAttr('Min',($data->portfolio_alternatives_min/100)*($data->portfolio_alternative_directholdings_min/100));

        $assettypeweights_child_values_value4_attr_max = new \DOMAttr('Max',($data->portfolio_alternatives_max/100)*($data->portfolio_alternative_directholdings_max/100));
        }
        else
        {
        $assettypeweights_child_values_value4_attr_ischecked = new \DOMAttr('IsChecked','false');

        $assettypeweights_child_values_value4_attr_min = new \DOMAttr('Min','0');

        $assettypeweights_child_values_value4_attr_max = new \DOMAttr('Max','1');
        }
     
        $assettypeweights_child_values_value4->setAttributeNode($assettypeweights_child_values_value4_attr_name);

        $assettypeweights_child_values_value4->setAttributeNode($assettypeweights_child_values_value4_attr_ischecked);

        $assettypeweights_child_values_value4->setAttributeNode($assettypeweights_child_values_value4_attr_min);

        $assettypeweights_child_values_value4->setAttributeNode($assettypeweights_child_values_value4_attr_max);

        $assettypeweights_child_values->appendChild($assettypeweights_child_values_value4);  

        $assettypeweights_node->appendChild($assettypeweights_child_values);

        $constraints_node->appendChild($assettypeweights_node);

        $currencyweights_node = $dom->createElement('CurrencyWeights');

        $currencyweights_node_attr = new \DOMAttr('IsChecked','true');

        $currencyweights_node->setAttributeNode($currencyweights_node_attr);

        $currencyweights_child_values = $dom->createElement('Values');

        $currencyweights_child_values_value1 = $dom->createElement('Value');

        $currencyweights_child_values_value1_attr_name = new \DOMAttr('Name','USD');

        $currencyweights_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','true');

        $currencyweights_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $currencyweights_child_values_value1_attr_max = new \DOMAttr('Max','1');

        $currencyweights_child_values_value1->setAttributeNode($currencyweights_child_values_value1_attr_name);

        $currencyweights_child_values_value1->setAttributeNode($currencyweights_child_values_value1_attr_ischecked);

        $currencyweights_child_values_value1->setAttributeNode($currencyweights_child_values_value1_attr_min);

        $currencyweights_child_values_value1->setAttributeNode($currencyweights_child_values_value1_attr_max); 

        $currencyweights_child_values->appendChild($currencyweights_child_values_value1); 

        $currencyweights_child_values_value2 = $dom->createElement('Value');

        $currencyweights_child_values_value2_attr_name = new \DOMAttr('Name','[Not Classified]');

        $currencyweights_child_values_value2_attr_ischecked = new \DOMAttr('IsChecked','true');

        $currencyweights_child_values_value2_attr_min = new \DOMAttr('Min','0');

        $currencyweights_child_values_value2_attr_max = new \DOMAttr('Max','1');

        $currencyweights_child_values_value2->setAttributeNode($currencyweights_child_values_value2_attr_name);

        $currencyweights_child_values_value2->setAttributeNode($currencyweights_child_values_value2_attr_ischecked);

        $currencyweights_child_values_value2->setAttributeNode($currencyweights_child_values_value2_attr_min);

        $currencyweights_child_values_value2->setAttributeNode($currencyweights_child_values_value2_attr_max); 

        $currencyweights_child_values->appendChild($currencyweights_child_values_value1);  
 
        $currencyweights_node->appendChild($currencyweights_child_values);

        $constraints_node->appendChild($currencyweights_node);

        $risksliquidity_node = $dom->createElement('RisksLiquidity');

        $risksliquidity_node_attr = new \DOMAttr('IsChecked','true');

        $risksliquidity_node->setAttributeNode($risksliquidity_node_attr);

        $risksliquidity_child_values = $dom->createElement('Values');

        $risksliquidity_child_values_value1 = $dom->createElement('Value');

        $risksliquidity_child_values_value1_attr_name = new \DOMAttr('Name','In 1 month');

        $risksliquidity_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','true');

        $risksliquidity_child_values_value1_attr_min = new \DOMAttr('Min',$data->portfolio_liquidity/100);

        $risksliquidity_child_values_value1_attr_max = new \DOMAttr('Max','1');

        $risksliquidity_child_values_value1->setAttributeNode($risksliquidity_child_values_value1_attr_name);

        $risksliquidity_child_values_value1->setAttributeNode($risksliquidity_child_values_value1_attr_ischecked);

        $risksliquidity_child_values_value1->setAttributeNode($risksliquidity_child_values_value1_attr_min);

        $risksliquidity_child_values_value1->setAttributeNode($risksliquidity_child_values_value1_attr_max); 

        $risksliquidity_child_values->appendChild($risksliquidity_child_values_value1);  

        $risksliquidity_node->appendChild($risksliquidity_child_values);

        $constraints_node->appendChild($risksliquidity_node);


        $risksportstatistics_node = $dom->createElement('RisksPortfolioStatistics');

        $risksportstatistics_node_attr = new \DOMAttr('IsChecked','true');

        $risksportstatistics_node->setAttributeNode($risksportstatistics_node_attr);

        $risksportstatistics_child_values = $dom->createElement('Values');

        $risksportstatistics_child_values_value1 = $dom->createElement('Value');

        $risksportstatistics_child_values_value1_attr_name = new \DOMAttr('Name','Max Drawdown');

        $risksportstatistics_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','true');

        $risksportstatistics_child_values_value1_attr_min = new \DOMAttr('Min','-0,3');

        $risksportstatistics_child_values_value1_attr_max = new \DOMAttr('Max',$data->portfolio_expect_maxdrawdown/100);

        $risksportstatistics_child_values_value1->setAttributeNode($risksportstatistics_child_values_value1_attr_name);

        $risksportstatistics_child_values_value1->setAttributeNode($risksportstatistics_child_values_value1_attr_ischecked);

        $risksportstatistics_child_values_value1->setAttributeNode($risksportstatistics_child_values_value1_attr_min);

        $risksportstatistics_child_values_value1->setAttributeNode($risksportstatistics_child_values_value1_attr_max); 

        $risksportstatistics_child_values->appendChild($risksportstatistics_child_values_value1);  

        $risksportstatistics_node->appendChild($risksportstatistics_child_values);

        $constraints_node->appendChild($risksportstatistics_node);



        $exposurenet_node = $dom->createElement('ExposureNet');

        $exposurenet_node_attr = new \DOMAttr('IsChecked','true');

        $exposurenet_node->setAttributeNode($exposurenet_node_attr);

        $exposurenet_child_values = $dom->createElement('Values');

        $exposurenet_child_values_value1 = $dom->createElement('Value');

        $exposurenet_child_values_value1_attr_name = new \DOMAttr('Name','APG Rating Exposure');

        $exposurenet_child_values_value1_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value1_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value1_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value1->setAttributeNode($exposurenet_child_values_value1_attr_name);

        $exposurenet_child_values_value1->setAttributeNode($exposurenet_child_values_value1_attr_ischecked);

        $exposurenet_child_values_value1->setAttributeNode($exposurenet_child_values_value1_attr_min);

        $exposurenet_child_values_value1->setAttributeNode($exposurenet_child_values_value1_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value1);  



        $exposurenet_child_values_value3 = $dom->createElement('Value');

        $exposurenet_child_values_value3_attr_name = new \DOMAttr('Name','AAA');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "1" || $data->portfolio_exposure_value == "2" || $data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value3_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value3_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value3_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value3_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value3_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value3_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value3_attr_max = new \DOMAttr('Max','1');    
      
        }
        
              
        $exposurenet_child_values_value3->setAttributeNode($exposurenet_child_values_value3_attr_name);

        $exposurenet_child_values_value3->setAttributeNode($exposurenet_child_values_value3_attr_ischecked);

        $exposurenet_child_values_value3->setAttributeNode($exposurenet_child_values_value3_attr_min);

        $exposurenet_child_values_value3->setAttributeNode($exposurenet_child_values_value3_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value3); 


        $exposurenet_child_values_value4 = $dom->createElement('Value');

        $exposurenet_child_values_value4_attr_name = new \DOMAttr('Name','AA+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value4_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "2" || $data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value4_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value4_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value4_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value4_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value4_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value4_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value4_attr_max = new \DOMAttr('Max','1');    
      
        }
        
        $exposurenet_child_values_value4->setAttributeNode($exposurenet_child_values_value4_attr_name);

        $exposurenet_child_values_value4->setAttributeNode($exposurenet_child_values_value4_attr_ischecked);

        $exposurenet_child_values_value4->setAttributeNode($exposurenet_child_values_value4_attr_min);

        $exposurenet_child_values_value4->setAttributeNode($exposurenet_child_values_value4_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value4); 


        $exposurenet_child_values_value5 = $dom->createElement('Value');

        $exposurenet_child_values_value5_attr_name = new \DOMAttr('Name','AA');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value5_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "2" || $data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value5_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value5_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value5_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value5_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value5_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value5_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value5_attr_max = new \DOMAttr('Max','1');    
      
        }

        $exposurenet_child_values_value5->setAttributeNode($exposurenet_child_values_value5_attr_name);

        $exposurenet_child_values_value5->setAttributeNode($exposurenet_child_values_value5_attr_ischecked);

        $exposurenet_child_values_value5->setAttributeNode($exposurenet_child_values_value5_attr_min);

        $exposurenet_child_values_value5->setAttributeNode($exposurenet_child_values_value5_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value5); 


        $exposurenet_child_values_value6 = $dom->createElement('Value');

        $exposurenet_child_values_value6_attr_name = new \DOMAttr('Name','AA-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value6_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value6_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value6_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value6_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value6_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value6_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value6_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value6_attr_max = new \DOMAttr('Max','1');    
      
        }
        

        $exposurenet_child_values_value6->setAttributeNode($exposurenet_child_values_value6_attr_name);

        $exposurenet_child_values_value6->setAttributeNode($exposurenet_child_values_value6_attr_ischecked);

        $exposurenet_child_values_value6->setAttributeNode($exposurenet_child_values_value6_attr_min);

        $exposurenet_child_values_value6->setAttributeNode($exposurenet_child_values_value6_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value6); 


        $exposurenet_child_values_value7 = $dom->createElement('Value');

        $exposurenet_child_values_value7_attr_name = new \DOMAttr('Name','A+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value7_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value7_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value7_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value7_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value7_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value7_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value7_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value7_attr_max = new \DOMAttr('Max','1');    
      
        }
        

        $exposurenet_child_values_value7->setAttributeNode($exposurenet_child_values_value7_attr_name);

        $exposurenet_child_values_value7->setAttributeNode($exposurenet_child_values_value7_attr_ischecked);

        $exposurenet_child_values_value7->setAttributeNode($exposurenet_child_values_value7_attr_min);

        $exposurenet_child_values_value7->setAttributeNode($exposurenet_child_values_value7_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value7); 


        $exposurenet_child_values_value8 = $dom->createElement('Value');

        $exposurenet_child_values_value8_attr_name = new \DOMAttr('Name','A');

       if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value8_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "3" || $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value8_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value8_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value8_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value8_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value8_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value8_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value8_attr_max = new \DOMAttr('Max','1');    
      
        }
        
        $exposurenet_child_values_value8->setAttributeNode($exposurenet_child_values_value8_attr_name);

        $exposurenet_child_values_value8->setAttributeNode($exposurenet_child_values_value8_attr_ischecked);

        $exposurenet_child_values_value8->setAttributeNode($exposurenet_child_values_value8_attr_min);

        $exposurenet_child_values_value8->setAttributeNode($exposurenet_child_values_value8_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value8); 


        $exposurenet_child_values_value9 = $dom->createElement('Value');

        $exposurenet_child_values_value9_attr_name = new \DOMAttr('Name','A-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value9_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value9_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value9_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100);  
        }
        else
        {
        $exposurenet_child_values_value9_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value9_attr_max = new \DOMAttr('Max','0');    
        }
        }
        else
        {
        $exposurenet_child_values_value9_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value9_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value9_attr_max = new \DOMAttr('Max','1');    
      
        }
        
        $exposurenet_child_values_value9->setAttributeNode($exposurenet_child_values_value9_attr_name);

        $exposurenet_child_values_value9->setAttributeNode($exposurenet_child_values_value9_attr_ischecked);

        $exposurenet_child_values_value9->setAttributeNode($exposurenet_child_values_value9_attr_min);

        $exposurenet_child_values_value9->setAttributeNode($exposurenet_child_values_value9_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value9); 


        $exposurenet_child_values_value10 = $dom->createElement('Value');

        $exposurenet_child_values_value10_attr_name = new \DOMAttr('Name','BBB+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value10_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value10_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value10_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value10_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value10_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value10_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value10_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value10_attr_max = new \DOMAttr('Max','1');   
      
        }
        
        $exposurenet_child_values_value10->setAttributeNode($exposurenet_child_values_value10_attr_name);

        $exposurenet_child_values_value10->setAttributeNode($exposurenet_child_values_value10_attr_ischecked);

        $exposurenet_child_values_value10->setAttributeNode($exposurenet_child_values_value10_attr_min);

        $exposurenet_child_values_value10->setAttributeNode($exposurenet_child_values_value10_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value10); 


        $exposurenet_child_values_value11 = $dom->createElement('Value');

        $exposurenet_child_values_value11_attr_name = new \DOMAttr('Name','BBB');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value11_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "4" || $data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value11_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value11_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value11_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value11_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value11_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value11_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value11_attr_max = new \DOMAttr('Max','1');   
      
        }
        

        $exposurenet_child_values_value11->setAttributeNode($exposurenet_child_values_value11_attr_name);

        $exposurenet_child_values_value11->setAttributeNode($exposurenet_child_values_value11_attr_ischecked);

        $exposurenet_child_values_value11->setAttributeNode($exposurenet_child_values_value11_attr_min);

        $exposurenet_child_values_value11->setAttributeNode($exposurenet_child_values_value11_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value11); 


        $exposurenet_child_values_value12 = $dom->createElement('Value');

        $exposurenet_child_values_value12_attr_name = new \DOMAttr('Name','BBB-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value12_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value12_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value12_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value12_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value12_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value12_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value12_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value12_attr_max = new \DOMAttr('Max','1');   
      
        }
        

        $exposurenet_child_values_value12->setAttributeNode($exposurenet_child_values_value12_attr_name);

        $exposurenet_child_values_value12->setAttributeNode($exposurenet_child_values_value12_attr_ischecked);

        $exposurenet_child_values_value12->setAttributeNode($exposurenet_child_values_value12_attr_min);

        $exposurenet_child_values_value12->setAttributeNode($exposurenet_child_values_value12_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value12); 



        $exposurenet_child_values_value13 = $dom->createElement('Value');

        $exposurenet_child_values_value13_attr_name = new \DOMAttr('Name','BB+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value13_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value13_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value13_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value13_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value13_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value13_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value13_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value13_attr_max = new \DOMAttr('Max','1');   
      
        }
        

        $exposurenet_child_values_value13->setAttributeNode($exposurenet_child_values_value13_attr_name);

        $exposurenet_child_values_value13->setAttributeNode($exposurenet_child_values_value13_attr_ischecked);

        $exposurenet_child_values_value13->setAttributeNode($exposurenet_child_values_value13_attr_min);

        $exposurenet_child_values_value13->setAttributeNode($exposurenet_child_values_value13_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value13); 



        $exposurenet_child_values_value14 = $dom->createElement('Value');

        $exposurenet_child_values_value14_attr_name = new \DOMAttr('Name','BB');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value14_attr_ischecked = new \DOMAttr('IsChecked','true');

        if($data->portfolio_exposure_value == "5" || $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value14_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value14_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value14_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value14_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value14_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value14_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value14_attr_max = new \DOMAttr('Max','1');   
      
        }
        

        $exposurenet_child_values_value14->setAttributeNode($exposurenet_child_values_value14_attr_name);

        $exposurenet_child_values_value14->setAttributeNode($exposurenet_child_values_value14_attr_ischecked);

        $exposurenet_child_values_value14->setAttributeNode($exposurenet_child_values_value14_attr_min);

        $exposurenet_child_values_value14->setAttributeNode($exposurenet_child_values_value14_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value14); 



        $exposurenet_child_values_value15 = $dom->createElement('Value');

        $exposurenet_child_values_value15_attr_name = new \DOMAttr('Name','BB-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value15_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value15_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value15_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value15_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value15_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value15_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value15_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value15_attr_max = new \DOMAttr('Max','1');   
      
        }
        
        $exposurenet_child_values_value15->setAttributeNode($exposurenet_child_values_value15_attr_name);

        $exposurenet_child_values_value15->setAttributeNode($exposurenet_child_values_value15_attr_ischecked);

        $exposurenet_child_values_value15->setAttributeNode($exposurenet_child_values_value15_attr_min);

        $exposurenet_child_values_value15->setAttributeNode($exposurenet_child_values_value15_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value15); 



        $exposurenet_child_values_value16 = $dom->createElement('Value');

        $exposurenet_child_values_value16_attr_name = new \DOMAttr('Name','B+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value16_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value16_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value16_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value16_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value16_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value16_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value16_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value16_attr_max = new \DOMAttr('Max','1');   
      
        }
        
        $exposurenet_child_values_value16->setAttributeNode($exposurenet_child_values_value16_attr_name);

        $exposurenet_child_values_value16->setAttributeNode($exposurenet_child_values_value16_attr_ischecked);

        $exposurenet_child_values_value16->setAttributeNode($exposurenet_child_values_value16_attr_min);

        $exposurenet_child_values_value16->setAttributeNode($exposurenet_child_values_value16_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value16); 


        $exposurenet_child_values_value17 = $dom->createElement('Value');

        $exposurenet_child_values_value17_attr_name = new \DOMAttr('Name','B');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value17_attr_ischecked = new \DOMAttr('IsChecked','true');

        if( $data->portfolio_exposure_value == "6" )
        {
        $exposurenet_child_values_value17_attr_min = new \DOMAttr('Min',$data->portfolio_exposure_min/100);

        $exposurenet_child_values_value17_attr_max = new \DOMAttr('Max',$data->portfolio_exposure_max/100); 
        }
        else
        {
        $exposurenet_child_values_value17_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value17_attr_max = new \DOMAttr('Max','0');   
        }
        }
        else
        {
        $exposurenet_child_values_value17_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value17_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value17_attr_max = new \DOMAttr('Max','1');   
      
        }
        
        $exposurenet_child_values_value17->setAttributeNode($exposurenet_child_values_value17_attr_name);

        $exposurenet_child_values_value17->setAttributeNode($exposurenet_child_values_value17_attr_ischecked);

        $exposurenet_child_values_value17->setAttributeNode($exposurenet_child_values_value17_attr_min);

        $exposurenet_child_values_value17->setAttributeNode($exposurenet_child_values_value17_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value17); 


        $exposurenet_child_values_value18 = $dom->createElement('Value');

        $exposurenet_child_values_value18_attr_name = new \DOMAttr('Name','B-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value18_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value18_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value18_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value18_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value18_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value18_attr_max = new \DOMAttr('Max','1');
        }
        
        $exposurenet_child_values_value18->setAttributeNode($exposurenet_child_values_value18_attr_name);

        $exposurenet_child_values_value18->setAttributeNode($exposurenet_child_values_value18_attr_ischecked);

        $exposurenet_child_values_value18->setAttributeNode($exposurenet_child_values_value18_attr_min);

        $exposurenet_child_values_value18->setAttributeNode($exposurenet_child_values_value18_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value18); 



        $exposurenet_child_values_value19 = $dom->createElement('Value');

        $exposurenet_child_values_value19_attr_name = new \DOMAttr('Name','CCC+');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value19_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value19_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value19_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value19_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value19_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value19_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value19->setAttributeNode($exposurenet_child_values_value19_attr_name);

        $exposurenet_child_values_value19->setAttributeNode($exposurenet_child_values_value19_attr_ischecked);

        $exposurenet_child_values_value19->setAttributeNode($exposurenet_child_values_value19_attr_min);

        $exposurenet_child_values_value19->setAttributeNode($exposurenet_child_values_value19_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value19); 



        $exposurenet_child_values_value20 = $dom->createElement('Value');

        $exposurenet_child_values_value20_attr_name = new \DOMAttr('Name','CCC');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value20_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value20_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value20_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value20_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value20_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value20_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value20->setAttributeNode($exposurenet_child_values_value20_attr_name);

        $exposurenet_child_values_value20->setAttributeNode($exposurenet_child_values_value20_attr_ischecked);

        $exposurenet_child_values_value20->setAttributeNode($exposurenet_child_values_value20_attr_min);

        $exposurenet_child_values_value20->setAttributeNode($exposurenet_child_values_value20_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value20); 



        $exposurenet_child_values_value21 = $dom->createElement('Value');

        $exposurenet_child_values_value21_attr_name = new \DOMAttr('Name','CCC-');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value21_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value21_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value21_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value21_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value21_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value21_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value21->setAttributeNode($exposurenet_child_values_value21_attr_name);

        $exposurenet_child_values_value21->setAttributeNode($exposurenet_child_values_value21_attr_ischecked);

        $exposurenet_child_values_value21->setAttributeNode($exposurenet_child_values_value21_attr_min);

        $exposurenet_child_values_value21->setAttributeNode($exposurenet_child_values_value21_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value21); 



        $exposurenet_child_values_value22 = $dom->createElement('Value');

        $exposurenet_child_values_value22_attr_name = new \DOMAttr('Name','CC');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value22_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value22_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value22_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value22_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value22_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value22_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value22->setAttributeNode($exposurenet_child_values_value22_attr_name);

        $exposurenet_child_values_value22->setAttributeNode($exposurenet_child_values_value22_attr_ischecked);

        $exposurenet_child_values_value22->setAttributeNode($exposurenet_child_values_value22_attr_min);

        $exposurenet_child_values_value22->setAttributeNode($exposurenet_child_values_value22_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value22); 



        $exposurenet_child_values_value23 = $dom->createElement('Value');

        $exposurenet_child_values_value23_attr_name = new \DOMAttr('Name','C');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value23_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value23_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value23_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value23_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value23_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value23_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value23->setAttributeNode($exposurenet_child_values_value23_attr_name);

        $exposurenet_child_values_value23->setAttributeNode($exposurenet_child_values_value23_attr_ischecked);

        $exposurenet_child_values_value23->setAttributeNode($exposurenet_child_values_value23_attr_min);

        $exposurenet_child_values_value23->setAttributeNode($exposurenet_child_values_value23_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value23); 



        $exposurenet_child_values_value24 = $dom->createElement('Value');

        $exposurenet_child_values_value24_attr_name = new \DOMAttr('Name','DDD');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value24_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value24_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value24_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value24_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value24_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value24_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value24->setAttributeNode($exposurenet_child_values_value24_attr_name);

        $exposurenet_child_values_value24->setAttributeNode($exposurenet_child_values_value24_attr_ischecked);

        $exposurenet_child_values_value24->setAttributeNode($exposurenet_child_values_value24_attr_min);

        $exposurenet_child_values_value24->setAttributeNode($exposurenet_child_values_value24_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value24); 


        $exposurenet_child_values_value25 = $dom->createElement('Value');

        $exposurenet_child_values_value25_attr_name = new \DOMAttr('Name','DD');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value25_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value25_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value25_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value25_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value25_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value25_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value25->setAttributeNode($exposurenet_child_values_value25_attr_name);

        $exposurenet_child_values_value25->setAttributeNode($exposurenet_child_values_value25_attr_ischecked);

        $exposurenet_child_values_value25->setAttributeNode($exposurenet_child_values_value25_attr_min);

        $exposurenet_child_values_value25->setAttributeNode($exposurenet_child_values_value25_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value25); 



        $exposurenet_child_values_value26 = $dom->createElement('Value');

        $exposurenet_child_values_value26_attr_name = new \DOMAttr('Name','D');

        if($data->portfolio_exposure_status == "1")
        {
        $exposurenet_child_values_value26_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value26_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value26_attr_max = new \DOMAttr('Max','0');
        }
        else
        {
        $exposurenet_child_values_value26_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value26_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value26_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value26->setAttributeNode($exposurenet_child_values_value26_attr_name);

        $exposurenet_child_values_value26->setAttributeNode($exposurenet_child_values_value26_attr_ischecked);

        $exposurenet_child_values_value26->setAttributeNode($exposurenet_child_values_value26_attr_min);

        $exposurenet_child_values_value26->setAttributeNode($exposurenet_child_values_value26_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value26); 



        $exposurenet_child_values_value27 = $dom->createElement('Value');

        $exposurenet_child_values_value27_attr_name = new \DOMAttr('Name','NR');

        $exposurenet_child_values_value27_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value27_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value27_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value27->setAttributeNode($exposurenet_child_values_value27_attr_name);

        $exposurenet_child_values_value27->setAttributeNode($exposurenet_child_values_value27_attr_ischecked);

        $exposurenet_child_values_value27->setAttributeNode($exposurenet_child_values_value27_attr_min);

        $exposurenet_child_values_value27->setAttributeNode($exposurenet_child_values_value27_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value27); 



        $exposurenet_child_values_value28 = $dom->createElement('Value');

        $exposurenet_child_values_value28_attr_name = new \DOMAttr('Name','APG Regional Exposure');

        $exposurenet_child_values_value28_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value28_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value28_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value28->setAttributeNode($exposurenet_child_values_value28_attr_name);

        $exposurenet_child_values_value28->setAttributeNode($exposurenet_child_values_value28_attr_ischecked);

        $exposurenet_child_values_value28->setAttributeNode($exposurenet_child_values_value28_attr_min);

        $exposurenet_child_values_value28->setAttributeNode($exposurenet_child_values_value28_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value28); 



        $exposurenet_child_values_value29 = $dom->createElement('Value');

        $exposurenet_child_values_value29_attr_name = new \DOMAttr('Name','Asia Pacific');

        if($data->portfolio_asia_status == 1)
        {
         $exposurenet_child_values_value29_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value29_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value29_attr_min = new \DOMAttr('Min',$data->portfolio_asia_min/100);

        $exposurenet_child_values_value29_attr_max = new \DOMAttr('Max',$data->portfolio_asia_max/100);

        $exposurenet_child_values_value29->setAttributeNode($exposurenet_child_values_value29_attr_name);

        $exposurenet_child_values_value29->setAttributeNode($exposurenet_child_values_value29_attr_ischecked);

        $exposurenet_child_values_value29->setAttributeNode($exposurenet_child_values_value29_attr_min);

        $exposurenet_child_values_value29->setAttributeNode($exposurenet_child_values_value29_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value29); 

      
        $exposurenet_child_values_value30 = $dom->createElement('Value');

        $exposurenet_child_values_value30_attr_name = new \DOMAttr('Name','China');
         if($data->portfolio_china_status == "1")
        {
        $exposurenet_child_values_value30_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value30_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value30_attr_min = new \DOMAttr('Min',$data->portfolio_china_min/100);

        $exposurenet_child_values_value30_attr_max = new \DOMAttr('Max',$data->portfolio_china_max/100);

        $exposurenet_child_values_value30->setAttributeNode($exposurenet_child_values_value30_attr_name);

        $exposurenet_child_values_value30->setAttributeNode($exposurenet_child_values_value30_attr_ischecked);

        $exposurenet_child_values_value30->setAttributeNode($exposurenet_child_values_value30_attr_min);

        $exposurenet_child_values_value30->setAttributeNode($exposurenet_child_values_value30_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value30); 
       

  
        $exposurenet_child_values_value31 = $dom->createElement('Value');

        $exposurenet_child_values_value31_attr_name = new \DOMAttr('Name','South Korea');

        if($data->portfolio_south_korea_status == "1")
        {
        $exposurenet_child_values_value31_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value31_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value31_attr_min = new \DOMAttr('Min',$data->portfolio_south_korea_min/100);

        $exposurenet_child_values_value31_attr_max = new \DOMAttr('Max',$data->portfolio_south_korea_max/100);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_name);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_ischecked);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_min);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value31); 


        $exposurenet_child_values_value31 = $dom->createElement('Value');

        $exposurenet_child_values_value31_attr_name = new \DOMAttr('Name','Australia');

        if($data->portfolio_australia_status == "1")
        {
        $exposurenet_child_values_value31_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value31_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value31_attr_min = new \DOMAttr('Min',$data->portfolio_australia_min/100);

        $exposurenet_child_values_value31_attr_max = new \DOMAttr('Max',$data->portfolio_australia_max/100);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_name);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_ischecked);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_min);

        $exposurenet_child_values_value31->setAttributeNode($exposurenet_child_values_value31_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value31); 


        $exposurenet_child_values_value32 = $dom->createElement('Value');

        $exposurenet_child_values_value32_attr_name = new \DOMAttr('Name','Vietnam');

        if($data->portfolio_vietnam_status == "1")
        {
         $exposurenet_child_values_value32_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
         $exposurenet_child_values_value32_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value32_attr_min = new \DOMAttr('Min',$data->portfolio_vietnam_min/100);

        $exposurenet_child_values_value32_attr_max = new \DOMAttr('Max',$data->portfolio_vietnam_max/100);

        $exposurenet_child_values_value32->setAttributeNode($exposurenet_child_values_value32_attr_name);

        $exposurenet_child_values_value32->setAttributeNode($exposurenet_child_values_value32_attr_ischecked);

        $exposurenet_child_values_value32->setAttributeNode($exposurenet_child_values_value32_attr_min);

        $exposurenet_child_values_value32->setAttributeNode($exposurenet_child_values_value32_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value32); 


        $exposurenet_child_values_value33 = $dom->createElement('Value');

        $exposurenet_child_values_value33_attr_name = new \DOMAttr('Name','Singapore');

        if($data->portfolio_singapore_status == "1")
        {
        $exposurenet_child_values_value33_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value33_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value33_attr_min = new \DOMAttr('Min',$data->portfolio_singapore_min/100);

        $exposurenet_child_values_value33_attr_max = new \DOMAttr('Max',$data->portfolio_singapore_max/100);

        $exposurenet_child_values_value33->setAttributeNode($exposurenet_child_values_value33_attr_name);

        $exposurenet_child_values_value33->setAttributeNode($exposurenet_child_values_value33_attr_ischecked);

        $exposurenet_child_values_value33->setAttributeNode($exposurenet_child_values_value33_attr_min);

        $exposurenet_child_values_value33->setAttributeNode($exposurenet_child_values_value33_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value33); 


        $exposurenet_child_values_value34 = $dom->createElement('Value');

        $exposurenet_child_values_value34_attr_name = new \DOMAttr('Name','Malaysia');

        if($data->portfolio_malaysia_status == "1")
        {
        $exposurenet_child_values_value34_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value34_attr_ischecked = new \DOMAttr('IsChecked','false');
        }


        $exposurenet_child_values_value34_attr_min = new \DOMAttr('Min',$data->portfolio_malaysia_min/100);

        $exposurenet_child_values_value34_attr_max = new \DOMAttr('Max',$data->portfolio_malaysia_max/100);

        $exposurenet_child_values_value34->setAttributeNode($exposurenet_child_values_value34_attr_name);

        $exposurenet_child_values_value34->setAttributeNode($exposurenet_child_values_value34_attr_ischecked);

        $exposurenet_child_values_value34->setAttributeNode($exposurenet_child_values_value34_attr_min);

        $exposurenet_child_values_value34->setAttributeNode($exposurenet_child_values_value34_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value34); 


      
        $exposurenet_child_values_value35 = $dom->createElement('Value');

        $exposurenet_child_values_value35_attr_name = new \DOMAttr('Name','India');

        if($data->portfolio_india_status == "1")
        {
        $exposurenet_child_values_value35_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value35_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value35_attr_min = new \DOMAttr('Min',$data->portfolio_india_min/100);

        $exposurenet_child_values_value35_attr_max = new \DOMAttr('Max',$data->portfolio_india_max/100);

        $exposurenet_child_values_value35->setAttributeNode($exposurenet_child_values_value35_attr_name);

        $exposurenet_child_values_value35->setAttributeNode($exposurenet_child_values_value35_attr_ischecked);

        $exposurenet_child_values_value35->setAttributeNode($exposurenet_child_values_value35_attr_min);

        $exposurenet_child_values_value35->setAttributeNode($exposurenet_child_values_value35_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value35); 
        
     
       
        $exposurenet_child_values_value36 = $dom->createElement('Value');

        $exposurenet_child_values_value36_attr_name = new \DOMAttr('Name','Japan');

        if($data->portfolio_japan_status == "1")
        {
        $exposurenet_child_values_value36_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value36_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value36_attr_min = new \DOMAttr('Min',$data->portfolio_japan_min/100);

        $exposurenet_child_values_value36_attr_max = new \DOMAttr('Max',$data->portfolio_japan_max/100);

        $exposurenet_child_values_value36->setAttributeNode($exposurenet_child_values_value36_attr_name);

        $exposurenet_child_values_value36->setAttributeNode($exposurenet_child_values_value36_attr_ischecked);

        $exposurenet_child_values_value36->setAttributeNode($exposurenet_child_values_value36_attr_min);

        $exposurenet_child_values_value36->setAttributeNode($exposurenet_child_values_value36_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value36); 

     

        $exposurenet_child_values_value37 = $dom->createElement('Value');

        $exposurenet_child_values_value37_attr_name = new \DOMAttr('Name','Bangladesh');

        if($data->portfolio_bangladesh_status == "1")
        {
        $exposurenet_child_values_value37_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value37_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value37_attr_min = new \DOMAttr('Min',$data->portfolio_bangladesh_min/100);

        $exposurenet_child_values_value37_attr_max = new \DOMAttr('Max',$data->portfolio_bangladesh_max/100);

        $exposurenet_child_values_value37->setAttributeNode($exposurenet_child_values_value37_attr_name);

        $exposurenet_child_values_value37->setAttributeNode($exposurenet_child_values_value37_attr_ischecked);

        $exposurenet_child_values_value37->setAttributeNode($exposurenet_child_values_value37_attr_min);

        $exposurenet_child_values_value37->setAttributeNode($exposurenet_child_values_value37_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value37); 


        $exposurenet_child_values_value38 = $dom->createElement('Value');

        $exposurenet_child_values_value38_attr_name = new \DOMAttr('Name','Cambodia');

        if($data->portfolio_cambodia_status == "1")
        {
        $exposurenet_child_values_value38_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value38_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value38_attr_min = new \DOMAttr('Min',$data->portfolio_cambodia_min/100);

        $exposurenet_child_values_value38_attr_max = new \DOMAttr('Max',$data->portfolio_cambodia_max/100);

        $exposurenet_child_values_value38->setAttributeNode($exposurenet_child_values_value38_attr_name);

        $exposurenet_child_values_value38->setAttributeNode($exposurenet_child_values_value38_attr_ischecked);

        $exposurenet_child_values_value38->setAttributeNode($exposurenet_child_values_value38_attr_min);

        $exposurenet_child_values_value38->setAttributeNode($exposurenet_child_values_value38_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value38); 



        $exposurenet_child_values_value39 = $dom->createElement('Value');

        $exposurenet_child_values_value39_attr_name = new \DOMAttr('Name','Brunei');

        if($data->portfolio_brunei_status == "1")
        {
        $exposurenet_child_values_value39_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value39_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value39_attr_min = new \DOMAttr('Min',$data->portfolio_brunei_min/100);

        $exposurenet_child_values_value39_attr_max = new \DOMAttr('Max',$data->portfolio_brunei_max/100);

        $exposurenet_child_values_value39->setAttributeNode($exposurenet_child_values_value39_attr_name);

        $exposurenet_child_values_value39->setAttributeNode($exposurenet_child_values_value39_attr_ischecked);

        $exposurenet_child_values_value39->setAttributeNode($exposurenet_child_values_value39_attr_min);

        $exposurenet_child_values_value39->setAttributeNode($exposurenet_child_values_value39_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value39); 



        $exposurenet_child_values_value40 = $dom->createElement('Value');

        $exposurenet_child_values_value40_attr_name = new \DOMAttr('Name','Hong Kong');

        if($data->portfolio_hong_kong_status == "1")
        {
        $exposurenet_child_values_value40_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value40_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value40_attr_min = new \DOMAttr('Min',$data->portfolio_hong_kong_min/100);

        $exposurenet_child_values_value40_attr_max = new \DOMAttr('Max',$data->portfolio_hong_kong_max/100);

        $exposurenet_child_values_value40->setAttributeNode($exposurenet_child_values_value40_attr_name);

        $exposurenet_child_values_value40->setAttributeNode($exposurenet_child_values_value40_attr_ischecked);

        $exposurenet_child_values_value40->setAttributeNode($exposurenet_child_values_value40_attr_min);

        $exposurenet_child_values_value40->setAttributeNode($exposurenet_child_values_value40_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value40); 



        $exposurenet_child_values_value41 = $dom->createElement('Value');

        $exposurenet_child_values_value41_attr_name = new \DOMAttr('Name','Indonesia');

        if($data->portfolio_indonesia_status == "1")
        {
        $exposurenet_child_values_value41_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value41_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value41_attr_min = new \DOMAttr('Min',$data->portfolio_indonesia_min/100);

        $exposurenet_child_values_value41_attr_max = new \DOMAttr('Max',$data->portfolio_indonesia_max/100);

        $exposurenet_child_values_value41->setAttributeNode($exposurenet_child_values_value41_attr_name);

        $exposurenet_child_values_value41->setAttributeNode($exposurenet_child_values_value41_attr_ischecked);

        $exposurenet_child_values_value41->setAttributeNode($exposurenet_child_values_value41_attr_min);

        $exposurenet_child_values_value41->setAttributeNode($exposurenet_child_values_value41_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value41); 



        $exposurenet_child_values_value42 = $dom->createElement('Value');

        $exposurenet_child_values_value42_attr_name = new \DOMAttr('Name','Laos');

        if($data->portfolio_laos_status == "1")
        {
        $exposurenet_child_values_value42_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value42_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value42_attr_min = new \DOMAttr('Min',$data->portfolio_laos_min/100);

        $exposurenet_child_values_value42_attr_max = new \DOMAttr('Max',$data->portfolio_laos_max/100);

        $exposurenet_child_values_value42->setAttributeNode($exposurenet_child_values_value42_attr_name);

        $exposurenet_child_values_value42->setAttributeNode($exposurenet_child_values_value42_attr_ischecked);

        $exposurenet_child_values_value42->setAttributeNode($exposurenet_child_values_value42_attr_min);

        $exposurenet_child_values_value42->setAttributeNode($exposurenet_child_values_value42_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value42); 



        $exposurenet_child_values_value43 = $dom->createElement('Value');

        $exposurenet_child_values_value43_attr_name = new \DOMAttr('Name','Myanmar');

        if($data->portfolio_myanmar_status/100)
        {
        $exposurenet_child_values_value43_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value43_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value43_attr_min = new \DOMAttr('Min',$data->portfolio_myanmar_min/100);

        $exposurenet_child_values_value43_attr_max = new \DOMAttr('Max',$data->portfolio_myanmar_max/100);

        $exposurenet_child_values_value43->setAttributeNode($exposurenet_child_values_value43_attr_name);

        $exposurenet_child_values_value43->setAttributeNode($exposurenet_child_values_value43_attr_ischecked);

        $exposurenet_child_values_value43->setAttributeNode($exposurenet_child_values_value43_attr_min);

        $exposurenet_child_values_value43->setAttributeNode($exposurenet_child_values_value43_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value43); 



        $exposurenet_child_values_value44 = $dom->createElement('Value');

        $exposurenet_child_values_value44_attr_name = new \DOMAttr('Name','New Zealand');

        if($data->portfolio_new_zealand_status == "1")
        {
        $exposurenet_child_values_value44_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value44_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value44_attr_min = new \DOMAttr('Min',$data->portfolio_new_zealand_min/100);

        $exposurenet_child_values_value44_attr_max = new \DOMAttr('Max',$data->portfolio_new_zealand_max/100);

        $exposurenet_child_values_value44->setAttributeNode($exposurenet_child_values_value44_attr_name);

        $exposurenet_child_values_value44->setAttributeNode($exposurenet_child_values_value44_attr_ischecked);

        $exposurenet_child_values_value44->setAttributeNode($exposurenet_child_values_value44_attr_min);

        $exposurenet_child_values_value44->setAttributeNode($exposurenet_child_values_value44_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value44); 



        $exposurenet_child_values_value45 = $dom->createElement('Value');

        $exposurenet_child_values_value45_attr_name = new \DOMAttr('Name','Pakistan');

        if($data->portfolio_pakistan_status == "1")
        {
        $exposurenet_child_values_value45_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value45_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value45_attr_min = new \DOMAttr('Min',$data->portfolio_pakistan_min/100);

        $exposurenet_child_values_value45_attr_max = new \DOMAttr('Max',$data->portfolio_pakistan_max/100);

        $exposurenet_child_values_value45->setAttributeNode($exposurenet_child_values_value45_attr_name);

        $exposurenet_child_values_value45->setAttributeNode($exposurenet_child_values_value45_attr_ischecked);

        $exposurenet_child_values_value45->setAttributeNode($exposurenet_child_values_value45_attr_min);

        $exposurenet_child_values_value45->setAttributeNode($exposurenet_child_values_value45_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value45); 


        $exposurenet_child_values_value46 = $dom->createElement('Value');

        $exposurenet_child_values_value46_attr_name = new \DOMAttr('Name','Sri Lanka');

        if($data->portfolio_sri_lanka_status == "1")
        {
        $exposurenet_child_values_value46_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value46_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value46_attr_min = new \DOMAttr('Min',$data->portfolio_sri_lanka_min/100);

        $exposurenet_child_values_value46_attr_max = new \DOMAttr('Max',$data->portfolio_sri_lanka_max/100);

        $exposurenet_child_values_value46->setAttributeNode($exposurenet_child_values_value46_attr_name);

        $exposurenet_child_values_value46->setAttributeNode($exposurenet_child_values_value46_attr_ischecked);

        $exposurenet_child_values_value46->setAttributeNode($exposurenet_child_values_value46_attr_min);

        $exposurenet_child_values_value46->setAttributeNode($exposurenet_child_values_value46_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value46); 


        $exposurenet_child_values_value47 = $dom->createElement('Value');

        $exposurenet_child_values_value47_attr_name = new \DOMAttr('Name','Taiwan');

        if($data->portfolio_taiwan_status == "1")
        {
        $exposurenet_child_values_value47_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value47_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value47_attr_min = new \DOMAttr('Min',$data->portfolio_taiwan_min/100);

        $exposurenet_child_values_value47_attr_max = new \DOMAttr('Max',$data->portfolio_taiwan_max/100);

        $exposurenet_child_values_value47->setAttributeNode($exposurenet_child_values_value47_attr_name);

        $exposurenet_child_values_value47->setAttributeNode($exposurenet_child_values_value47_attr_ischecked);

        $exposurenet_child_values_value47->setAttributeNode($exposurenet_child_values_value47_attr_min);

        $exposurenet_child_values_value47->setAttributeNode($exposurenet_child_values_value47_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value47); 


        $exposurenet_child_values_value48 = $dom->createElement('Value');

        $exposurenet_child_values_value48_attr_name = new \DOMAttr('Name','Thailand');

        if($data->portfolio_thailand_status == "1")
        {
        $exposurenet_child_values_value48_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value48_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value48_attr_min = new \DOMAttr('Min',$data->portfolio_thailand_min/100);

        $exposurenet_child_values_value48_attr_max = new \DOMAttr('Max',$data->portfolio_thailand_max/100);

        $exposurenet_child_values_value48->setAttributeNode($exposurenet_child_values_value48_attr_name);

        $exposurenet_child_values_value48->setAttributeNode($exposurenet_child_values_value48_attr_ischecked);

        $exposurenet_child_values_value48->setAttributeNode($exposurenet_child_values_value48_attr_min);

        $exposurenet_child_values_value48->setAttributeNode($exposurenet_child_values_value48_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value48); 

                
        $exposurenet_child_values_value49 = $dom->createElement('Value');

        $exposurenet_child_values_value49_attr_name = new \DOMAttr('Name','Americas');

        if($data->portfolio_americas_status == "1")
        {
        $exposurenet_child_values_value49_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value49_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value49_attr_min = new \DOMAttr('Min',$data->portfolio_americas_min/100);

        $exposurenet_child_values_value49_attr_max = new \DOMAttr('Max',$data->portfolio_americas_max/100);

        $exposurenet_child_values_value49->setAttributeNode($exposurenet_child_values_value49_attr_name);

        $exposurenet_child_values_value49->setAttributeNode($exposurenet_child_values_value49_attr_ischecked);

        $exposurenet_child_values_value49->setAttributeNode($exposurenet_child_values_value49_attr_min);

        $exposurenet_child_values_value49->setAttributeNode($exposurenet_child_values_value49_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value49);
        
       

        $exposurenet_child_values_value50 = $dom->createElement('Value');

        $exposurenet_child_values_value50_attr_name = new \DOMAttr('Name','U.S.');

        if($data->portfolio_us_status == "1")
        {
        $exposurenet_child_values_value50_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value50_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value50_attr_min = new \DOMAttr('Min',$data->portfolio_us_min/100);

        $exposurenet_child_values_value50_attr_max = new \DOMAttr('Max',$data->portfolio_us_max/100);

        $exposurenet_child_values_value50->setAttributeNode($exposurenet_child_values_value50_attr_name);

        $exposurenet_child_values_value50->setAttributeNode($exposurenet_child_values_value50_attr_ischecked);

        $exposurenet_child_values_value50->setAttributeNode($exposurenet_child_values_value50_attr_min);

        $exposurenet_child_values_value50->setAttributeNode($exposurenet_child_values_value50_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value50); 


        $exposurenet_child_values_value51 = $dom->createElement('Value');

        $exposurenet_child_values_value51_attr_name = new \DOMAttr('Name','Canada');

        if($data->portfolio_canada_status == "1")
        {
        $exposurenet_child_values_value51_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value51_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value51_attr_min = new \DOMAttr('Min',$data->portfolio_canada_min/100);

        $exposurenet_child_values_value51_attr_max = new \DOMAttr('Max',$data->portfolio_canada_max/100);

        $exposurenet_child_values_value51->setAttributeNode($exposurenet_child_values_value51_attr_name);

        $exposurenet_child_values_value51->setAttributeNode($exposurenet_child_values_value51_attr_ischecked);

        $exposurenet_child_values_value51->setAttributeNode($exposurenet_child_values_value51_attr_min);

        $exposurenet_child_values_value51->setAttributeNode($exposurenet_child_values_value51_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value51); 


        $exposurenet_child_values_value52 = $dom->createElement('Value');

        $exposurenet_child_values_value52_attr_name = new \DOMAttr('Name','E.U. &amp; U.K.');

        if($data->portfolio_ec_uk_status == "1")
        {
        $exposurenet_child_values_value52_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value52_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value52_attr_min = new \DOMAttr('Min',$data->portfolio_ec_uk_min/100);

        $exposurenet_child_values_value52_attr_max = new \DOMAttr('Max',$data->portfolio_ec_uk_max/100);

        $exposurenet_child_values_value52->setAttributeNode($exposurenet_child_values_value52_attr_name);

        $exposurenet_child_values_value52->setAttributeNode($exposurenet_child_values_value52_attr_ischecked);

        $exposurenet_child_values_value52->setAttributeNode($exposurenet_child_values_value52_attr_min);

        $exposurenet_child_values_value52->setAttributeNode($exposurenet_child_values_value52_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value52); 


        $exposurenet_child_values_value53 = $dom->createElement('Value');

        $exposurenet_child_values_value53_attr_name = new \DOMAttr('Name','Netherlands');

        if($data->portfolio_netherland_status == "1")
        {
        $exposurenet_child_values_value53_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value53_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value53_attr_min = new \DOMAttr('Min',$data->portfolio_netherland_min/100);

        $exposurenet_child_values_value53_attr_max = new \DOMAttr('Max',$data->portfolio_netherland_max/100);

        $exposurenet_child_values_value53->setAttributeNode($exposurenet_child_values_value53_attr_name);

        $exposurenet_child_values_value53->setAttributeNode($exposurenet_child_values_value53_attr_ischecked);

        $exposurenet_child_values_value53->setAttributeNode($exposurenet_child_values_value53_attr_min);

        $exposurenet_child_values_value53->setAttributeNode($exposurenet_child_values_value53_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value53); 


        $exposurenet_child_values_value54 = $dom->createElement('Value');

        $exposurenet_child_values_value54_attr_name = new \DOMAttr('Name','France');

        if($data->portfolio_france_status == "1")
        {
        $exposurenet_child_values_value54_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value54_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value54_attr_min = new \DOMAttr('Min',$data->portfolio_france_min/100);

        $exposurenet_child_values_value54_attr_max = new \DOMAttr('Max',$data->portfolio_france_max/100);

        $exposurenet_child_values_value54->setAttributeNode($exposurenet_child_values_value54_attr_name);

        $exposurenet_child_values_value54->setAttributeNode($exposurenet_child_values_value54_attr_ischecked);

        $exposurenet_child_values_value54->setAttributeNode($exposurenet_child_values_value54_attr_min);

        $exposurenet_child_values_value54->setAttributeNode($exposurenet_child_values_value54_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value54); 


        $exposurenet_child_values_value55 = $dom->createElement('Value');

        $exposurenet_child_values_value55_attr_name = new \DOMAttr('Name','Italy');

        if($data->portfolio_Italy_status == "1")
        {
        $exposurenet_child_values_value55_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value55_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value55_attr_min = new \DOMAttr('Min',$data->portfolio_Italy_min/100);

        $exposurenet_child_values_value55_attr_max = new \DOMAttr('Max',$data->portfolio_Italy_max/100);

        $exposurenet_child_values_value55->setAttributeNode($exposurenet_child_values_value55_attr_name);

        $exposurenet_child_values_value55->setAttributeNode($exposurenet_child_values_value55_attr_ischecked);

        $exposurenet_child_values_value55->setAttributeNode($exposurenet_child_values_value55_attr_min);

        $exposurenet_child_values_value55->setAttributeNode($exposurenet_child_values_value55_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value55); 


        $exposurenet_child_values_value56 = $dom->createElement('Value');

        $exposurenet_child_values_value56_attr_name = new \DOMAttr('Name','Germany');

        if($data->portfolio_germany_status == "1")
        {
        $exposurenet_child_values_value56_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value56_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value56_attr_min = new \DOMAttr('Min',$data->portfolio_germany_min/100);

        $exposurenet_child_values_value56_attr_max = new \DOMAttr('Max',$data->portfolio_germany_max/100);

        $exposurenet_child_values_value56->setAttributeNode($exposurenet_child_values_value56_attr_name);

        $exposurenet_child_values_value56->setAttributeNode($exposurenet_child_values_value56_attr_ischecked);

        $exposurenet_child_values_value56->setAttributeNode($exposurenet_child_values_value56_attr_min);

        $exposurenet_child_values_value56->setAttributeNode($exposurenet_child_values_value56_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value56); 


        $exposurenet_child_values_value57 = $dom->createElement('Value');

        $exposurenet_child_values_value57_attr_name = new \DOMAttr('Name','Great Britain');

        if($data->portfolio_great_britain_status == "1")
        {
        $exposurenet_child_values_value57_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value57_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value57_attr_min = new \DOMAttr('Min',$data->portfolio_great_britain_min/100);

        $exposurenet_child_values_value57_attr_max = new \DOMAttr('Max',$data->portfolio_great_britain_max/100);

        $exposurenet_child_values_value57->setAttributeNode($exposurenet_child_values_value57_attr_name);

        $exposurenet_child_values_value57->setAttributeNode($exposurenet_child_values_value57_attr_ischecked);

        $exposurenet_child_values_value57->setAttributeNode($exposurenet_child_values_value57_attr_min);

        $exposurenet_child_values_value57->setAttributeNode($exposurenet_child_values_value57_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value57); 

        
        $exposurenet_child_values_value58 = $dom->createElement('Value');

        $exposurenet_child_values_value58_attr_name = new \DOMAttr('Name','Europe');

        if($data->portfolio_europe_status == "1")
        {
        $exposurenet_child_values_value58_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value58_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value58_attr_min = new \DOMAttr('Min',$data->portfolio_europe_min/100);

        $exposurenet_child_values_value58_attr_max = new \DOMAttr('Max',$data->portfolio_europe_max/100);

        $exposurenet_child_values_value58->setAttributeNode($exposurenet_child_values_value58_attr_name);

        $exposurenet_child_values_value58->setAttributeNode($exposurenet_child_values_value58_attr_ischecked);

        $exposurenet_child_values_value58->setAttributeNode($exposurenet_child_values_value58_attr_min);

        $exposurenet_child_values_value58->setAttributeNode($exposurenet_child_values_value58_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value58); 
               

        $exposurenet_child_values_value59 = $dom->createElement('Value');

        $exposurenet_child_values_value59_attr_name = new \DOMAttr('Name','Finland');

        if($data->portfolio_finland_status == "1")
        {
        $exposurenet_child_values_value59_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value59_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value59_attr_min = new \DOMAttr('Min',$data->portfolio_finland_min/100);

        $exposurenet_child_values_value59_attr_max = new \DOMAttr('Max',$data->portfolio_finland_max/100);

        $exposurenet_child_values_value59->setAttributeNode($exposurenet_child_values_value59_attr_name);

        $exposurenet_child_values_value59->setAttributeNode($exposurenet_child_values_value59_attr_ischecked);

        $exposurenet_child_values_value59->setAttributeNode($exposurenet_child_values_value59_attr_min);

        $exposurenet_child_values_value59->setAttributeNode($exposurenet_child_values_value59_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value59); 



        $exposurenet_child_values_value60 = $dom->createElement('Value');

        $exposurenet_child_values_value60_attr_name = new \DOMAttr('Name','Greece');

        if($data->portfolio_greece_status == "1")
        {
        $exposurenet_child_values_value60_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value60_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value60_attr_min = new \DOMAttr('Min',$data->portfolio_greece_min/100);

        $exposurenet_child_values_value60_attr_max = new \DOMAttr('Max',$data->portfolio_greece_max/100);

        $exposurenet_child_values_value60->setAttributeNode($exposurenet_child_values_value60_attr_name);

        $exposurenet_child_values_value60->setAttributeNode($exposurenet_child_values_value60_attr_ischecked);

        $exposurenet_child_values_value60->setAttributeNode($exposurenet_child_values_value60_attr_min);

        $exposurenet_child_values_value60->setAttributeNode($exposurenet_child_values_value60_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value60); 


        $exposurenet_child_values_value61 = $dom->createElement('Value');

        $exposurenet_child_values_value61_attr_name = new \DOMAttr('Name','Hungary');

        if($data->portfolio_hungary_status == "1")
        {
        $exposurenet_child_values_value61_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value61_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value61_attr_min = new \DOMAttr('Min',$data->portfolio_hungary_min/100);

        $exposurenet_child_values_value61_attr_max = new \DOMAttr('Max',$data->portfolio_hungary_max/100);

        $exposurenet_child_values_value61->setAttributeNode($exposurenet_child_values_value61_attr_name);

        $exposurenet_child_values_value61->setAttributeNode($exposurenet_child_values_value61_attr_ischecked);

        $exposurenet_child_values_value61->setAttributeNode($exposurenet_child_values_value61_attr_min);

        $exposurenet_child_values_value61->setAttributeNode($exposurenet_child_values_value61_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value61); 



        $exposurenet_child_values_value62 = $dom->createElement('Value');

        $exposurenet_child_values_value62_attr_name = new \DOMAttr('Name','Ireland');

        if($data->portfolio_ireland_status == "1")
        {
        $exposurenet_child_values_value62_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value62_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value62_attr_min = new \DOMAttr('Min',$data->portfolio_ireland_min/100);

        $exposurenet_child_values_value62_attr_max = new \DOMAttr('Max',$data->portfolio_ireland_max/100);

        $exposurenet_child_values_value62->setAttributeNode($exposurenet_child_values_value62_attr_name);

        $exposurenet_child_values_value62->setAttributeNode($exposurenet_child_values_value62_attr_ischecked);

        $exposurenet_child_values_value62->setAttributeNode($exposurenet_child_values_value62_attr_min);

        $exposurenet_child_values_value62->setAttributeNode($exposurenet_child_values_value62_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value62); 



        $exposurenet_child_values_value63 = $dom->createElement('Value');

        $exposurenet_child_values_value63_attr_name = new \DOMAttr('Name','Luxembourg');

        if($data->portfolio_luxembourg_status == "1")
        {
        $exposurenet_child_values_value63_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value63_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value63_attr_min = new \DOMAttr('Min',$data->portfolio_luxembourg_min/100);

        $exposurenet_child_values_value63_attr_max = new \DOMAttr('Max',$data->portfolio_luxembourg_max/100);

        $exposurenet_child_values_value63->setAttributeNode($exposurenet_child_values_value63_attr_name);

        $exposurenet_child_values_value63->setAttributeNode($exposurenet_child_values_value63_attr_ischecked);

        $exposurenet_child_values_value63->setAttributeNode($exposurenet_child_values_value63_attr_min);

        $exposurenet_child_values_value63->setAttributeNode($exposurenet_child_values_value63_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value63); 



        $exposurenet_child_values_value64 = $dom->createElement('Value');

        $exposurenet_child_values_value64_attr_name = new \DOMAttr('Name','Malta');

        if($data->portfolio_malta_status == "1")
        {
        $exposurenet_child_values_value64_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value64_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value64_attr_min = new \DOMAttr('Min',$data->portfolio_malta_min/100);

        $exposurenet_child_values_value64_attr_max = new \DOMAttr('Max',$data->portfolio_malta_max/100);

        $exposurenet_child_values_value64->setAttributeNode($exposurenet_child_values_value64_attr_name);

        $exposurenet_child_values_value64->setAttributeNode($exposurenet_child_values_value64_attr_ischecked);

        $exposurenet_child_values_value64->setAttributeNode($exposurenet_child_values_value64_attr_min);

        $exposurenet_child_values_value64->setAttributeNode($exposurenet_child_values_value64_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value64); 



        $exposurenet_child_values_value65 = $dom->createElement('Value');

        $exposurenet_child_values_value65_attr_name = new \DOMAttr('Name','Monaco');

        if($data->portfolio_monaco_status == "1")
        {
        $exposurenet_child_values_value65_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value65_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value65_attr_min = new \DOMAttr('Min',$data->portfolio_monaco_min/100);

        $exposurenet_child_values_value65_attr_max = new \DOMAttr('Max',$data->portfolio_monaco_max/100);

        $exposurenet_child_values_value65->setAttributeNode($exposurenet_child_values_value65_attr_name);

        $exposurenet_child_values_value65->setAttributeNode($exposurenet_child_values_value65_attr_ischecked);

        $exposurenet_child_values_value65->setAttributeNode($exposurenet_child_values_value65_attr_min);

        $exposurenet_child_values_value65->setAttributeNode($exposurenet_child_values_value65_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value65); 



        $exposurenet_child_values_value66 = $dom->createElement('Value');

        $exposurenet_child_values_value66_attr_name = new \DOMAttr('Name','Norway');

        if($data->portfolio_norway_status == "1")
        {
        $exposurenet_child_values_value66_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value66_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value66_attr_min = new \DOMAttr('Min',$data->portfolio_norway_min/100);

        $exposurenet_child_values_value66_attr_max = new \DOMAttr('Max',$data->portfolio_norway_max/100);

        $exposurenet_child_values_value66->setAttributeNode($exposurenet_child_values_value66_attr_name);

        $exposurenet_child_values_value66->setAttributeNode($exposurenet_child_values_value66_attr_ischecked);

        $exposurenet_child_values_value66->setAttributeNode($exposurenet_child_values_value66_attr_min);

        $exposurenet_child_values_value66->setAttributeNode($exposurenet_child_values_value66_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value66); 


        $exposurenet_child_values_value67 = $dom->createElement('Value');

        $exposurenet_child_values_value67_attr_name = new \DOMAttr('Name','Poland');

        if($data->portfolio_poland_status == "1")
        {
        $exposurenet_child_values_value67_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value67_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value67_attr_min = new \DOMAttr('Min',$data->portfolio_poland_min/100);

        $exposurenet_child_values_value67_attr_max = new \DOMAttr('Max',$data->portfolio_poland_max/100);

        $exposurenet_child_values_value67->setAttributeNode($exposurenet_child_values_value67_attr_name);

        $exposurenet_child_values_value67->setAttributeNode($exposurenet_child_values_value67_attr_ischecked);

        $exposurenet_child_values_value67->setAttributeNode($exposurenet_child_values_value67_attr_min);

        $exposurenet_child_values_value67->setAttributeNode($exposurenet_child_values_value67_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value67); 


        $exposurenet_child_values_value68 = $dom->createElement('Value');

        $exposurenet_child_values_value68_attr_name = new \DOMAttr('Name','Portugal');

        if($data->portfolio_portugal_status == "1")
        {
        $exposurenet_child_values_value68_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value68_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value68_attr_min = new \DOMAttr('Min',$data->portfolio_portugal_min/100);

        $exposurenet_child_values_value68_attr_max = new \DOMAttr('Max',$data->portfolio_portugal_max/100);

        $exposurenet_child_values_value68->setAttributeNode($exposurenet_child_values_value68_attr_name);

        $exposurenet_child_values_value68->setAttributeNode($exposurenet_child_values_value68_attr_ischecked);

        $exposurenet_child_values_value68->setAttributeNode($exposurenet_child_values_value68_attr_min);

        $exposurenet_child_values_value68->setAttributeNode($exposurenet_child_values_value68_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value68); 



        $exposurenet_child_values_value69 = $dom->createElement('Value');

        $exposurenet_child_values_value69_attr_name = new \DOMAttr('Name','Romania');

        if($data->portfolio_romania_status == "1")
        {
        $exposurenet_child_values_value69_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value69_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value69_attr_min = new \DOMAttr('Min',$data->portfolio_romania_min/100);

        $exposurenet_child_values_value69_attr_max = new \DOMAttr('Max',$data->portfolio_romania_max/100);

        $exposurenet_child_values_value69->setAttributeNode($exposurenet_child_values_value69_attr_name);

        $exposurenet_child_values_value69->setAttributeNode($exposurenet_child_values_value69_attr_ischecked);

        $exposurenet_child_values_value69->setAttributeNode($exposurenet_child_values_value69_attr_min);

        $exposurenet_child_values_value69->setAttributeNode($exposurenet_child_values_value69_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value69); 


        
        $exposurenet_child_values_value70 = $dom->createElement('Value');
 
        $exposurenet_child_values_value70_attr_name = new \DOMAttr('Name','Russia');

        if($data->portfolio_russia_status == "1")
        {
        $exposurenet_child_values_value70_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value70_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value70_attr_min = new \DOMAttr('Min',$data->portfolio_russia_min/100);

        $exposurenet_child_values_value70_attr_max = new \DOMAttr('Max',$data->portfolio_russia_max/100);

        $exposurenet_child_values_value70->setAttributeNode($exposurenet_child_values_value70_attr_name);

        $exposurenet_child_values_value70->setAttributeNode($exposurenet_child_values_value70_attr_ischecked);

        $exposurenet_child_values_value70->setAttributeNode($exposurenet_child_values_value70_attr_min);

        $exposurenet_child_values_value70->setAttributeNode($exposurenet_child_values_value70_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value70); 
       

        $exposurenet_child_values_value71 = $dom->createElement('Value');

        $exposurenet_child_values_value71_attr_name = new \DOMAttr('Name','Serbia');

        if($data->portfolio_serbia_status == "1")
        {
        $exposurenet_child_values_value71_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value71_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value71_attr_min = new \DOMAttr('Min',$data->portfolio_serbia_min/100);

        $exposurenet_child_values_value71_attr_max = new \DOMAttr('Max',$data->portfolio_serbia_max/100);

        $exposurenet_child_values_value71->setAttributeNode($exposurenet_child_values_value71_attr_name);

        $exposurenet_child_values_value71->setAttributeNode($exposurenet_child_values_value71_attr_ischecked);

        $exposurenet_child_values_value71->setAttributeNode($exposurenet_child_values_value71_attr_min);

        $exposurenet_child_values_value71->setAttributeNode($exposurenet_child_values_value71_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value71); 



        $exposurenet_child_values_value72 = $dom->createElement('Value');

        $exposurenet_child_values_value72_attr_name = new \DOMAttr('Name','Spain');

        if($data->portfolio_spain_status == "1")
        {
        $exposurenet_child_values_value72_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value72_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value72_attr_min = new \DOMAttr('Min',$data->portfolio_spain_min/100);

        $exposurenet_child_values_value72_attr_max = new \DOMAttr('Max',$data->portfolio_spain_max/100);

        $exposurenet_child_values_value72->setAttributeNode($exposurenet_child_values_value72_attr_name);

        $exposurenet_child_values_value72->setAttributeNode($exposurenet_child_values_value72_attr_ischecked);

        $exposurenet_child_values_value72->setAttributeNode($exposurenet_child_values_value72_attr_min);

        $exposurenet_child_values_value72->setAttributeNode($exposurenet_child_values_value72_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value72); 


        $exposurenet_child_values_value73 = $dom->createElement('Value');

        $exposurenet_child_values_value73_attr_name = new \DOMAttr('Name','Slovakia');

        if($data->portfolio_slovakia_status == "1")
        {
        $exposurenet_child_values_value73_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value73_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value73_attr_min = new \DOMAttr('Min',$data->portfolio_slovakia_min/100);

        $exposurenet_child_values_value73_attr_max = new \DOMAttr('Max',$data->portfolio_slovakia_max/100);

        $exposurenet_child_values_value73->setAttributeNode($exposurenet_child_values_value73_attr_name);

        $exposurenet_child_values_value73->setAttributeNode($exposurenet_child_values_value73_attr_ischecked);

        $exposurenet_child_values_value73->setAttributeNode($exposurenet_child_values_value73_attr_min);

        $exposurenet_child_values_value73->setAttributeNode($exposurenet_child_values_value73_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value73); 


        $exposurenet_child_values_value74 = $dom->createElement('Value');

        $exposurenet_child_values_value74_attr_name = new \DOMAttr('Name','Slovenia');

        if($data->portfolio_slovenia_status == "1")
        {
        $exposurenet_child_values_value74_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value74_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value74_attr_min = new \DOMAttr('Min',$data->portfolio_slovenia_min/100);

        $exposurenet_child_values_value74_attr_max = new \DOMAttr('Max',$data->portfolio_slovenia_max/100);

        $exposurenet_child_values_value74->setAttributeNode($exposurenet_child_values_value74_attr_name);

        $exposurenet_child_values_value74->setAttributeNode($exposurenet_child_values_value74_attr_ischecked);

        $exposurenet_child_values_value74->setAttributeNode($exposurenet_child_values_value74_attr_min);

        $exposurenet_child_values_value74->setAttributeNode($exposurenet_child_values_value74_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value74); 




        $exposurenet_child_values_value75 = $dom->createElement('Value');

        $exposurenet_child_values_value75_attr_name = new \DOMAttr('Name','Sweden');

        if($data->portfolio_sweden_status == "1")
        {
        $exposurenet_child_values_value75_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value75_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value75_attr_min = new \DOMAttr('Min',$data->portfolio_sweden_min/100);

        $exposurenet_child_values_value75_attr_max = new \DOMAttr('Max',$data->portfolio_sweden_max/100);

        $exposurenet_child_values_value75->setAttributeNode($exposurenet_child_values_value75_attr_name);

        $exposurenet_child_values_value75->setAttributeNode($exposurenet_child_values_value75_attr_ischecked);

        $exposurenet_child_values_value75->setAttributeNode($exposurenet_child_values_value75_attr_min);

        $exposurenet_child_values_value75->setAttributeNode($exposurenet_child_values_value75_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value75); 



        $exposurenet_child_values_value76 = $dom->createElement('Value');

        $exposurenet_child_values_value76_attr_name = new \DOMAttr('Name','Switzerland');

        if($data->portfolio_switzerland_status == "1")
        {
        $exposurenet_child_values_value76_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value76_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value76_attr_min = new \DOMAttr('Min',$data->portfolio_switzerland_min/100);

        $exposurenet_child_values_value76_attr_max = new \DOMAttr('Max',$data->portfolio_switzerland_max/100);

        $exposurenet_child_values_value76->setAttributeNode($exposurenet_child_values_value76_attr_name);

        $exposurenet_child_values_value76->setAttributeNode($exposurenet_child_values_value76_attr_ischecked);

        $exposurenet_child_values_value76->setAttributeNode($exposurenet_child_values_value76_attr_min);

        $exposurenet_child_values_value76->setAttributeNode($exposurenet_child_values_value76_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value76); 



        $exposurenet_child_values_value77 = $dom->createElement('Value');

        $exposurenet_child_values_value77_attr_name = new \DOMAttr('Name','Ukraine');

        if($data->portfolio_ukraine_status == "1")
        {
        $exposurenet_child_values_value77_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value77_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value77_attr_min = new \DOMAttr('Min',$data->portfolio_ukraine_min/100);

        $exposurenet_child_values_value77_attr_max = new \DOMAttr('Max',$data->portfolio_ukraine_max/100);

        $exposurenet_child_values_value77->setAttributeNode($exposurenet_child_values_value77_attr_name);

        $exposurenet_child_values_value77->setAttributeNode($exposurenet_child_values_value77_attr_ischecked);

        $exposurenet_child_values_value77->setAttributeNode($exposurenet_child_values_value77_attr_min);

        $exposurenet_child_values_value77->setAttributeNode($exposurenet_child_values_value77_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value77); 



        $exposurenet_child_values_value78 = $dom->createElement('Value');

        $exposurenet_child_values_value78_attr_name = new \DOMAttr('Name','Emerging Markets');

        if($data->portfolio_emerging_markets_status == "1")
        {
        $exposurenet_child_values_value78_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value78_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value78_attr_min = new \DOMAttr('Min',$data->portfolio_emerging_markets_min/100);

        $exposurenet_child_values_value78_attr_max = new \DOMAttr('Max',$data->portfolio_emerging_markets_max/100);

        $exposurenet_child_values_value78->setAttributeNode($exposurenet_child_values_value78_attr_name);

        $exposurenet_child_values_value78->setAttributeNode($exposurenet_child_values_value78_attr_ischecked);

        $exposurenet_child_values_value78->setAttributeNode($exposurenet_child_values_value78_attr_min);

        $exposurenet_child_values_value78->setAttributeNode($exposurenet_child_values_value78_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value78); 


        $exposurenet_child_values_value163 = $dom->createElement('Value');

        $exposurenet_child_values_value163_attr_name = new \DOMAttr('Name','Algeria');

        if($data->portfolio_algeria_status == "1")
        {
        $exposurenet_child_values_value163_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value163_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value163_attr_min = new \DOMAttr('Min',$data->portfolio_algeria_min/100);

        $exposurenet_child_values_value163_attr_max = new \DOMAttr('Max',$data->portfolio_algeria_max/100);

        $exposurenet_child_values_value163->setAttributeNode($exposurenet_child_values_value163_attr_name);

        $exposurenet_child_values_value163->setAttributeNode($exposurenet_child_values_value163_attr_ischecked);

        $exposurenet_child_values_value163->setAttributeNode($exposurenet_child_values_value163_attr_min);

        $exposurenet_child_values_value163->setAttributeNode($exposurenet_child_values_value163_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value163); 



        $exposurenet_child_values_value79 = $dom->createElement('Value');

        $exposurenet_child_values_value79_attr_name = new \DOMAttr('Name','Argentina');

        if($data->portfolio_argentina_status == "1")
        {
        $exposurenet_child_values_value79_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value79_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value79_attr_min = new \DOMAttr('Min',$data->portfolio_argentina_min/100);

        $exposurenet_child_values_value79_attr_max = new \DOMAttr('Max',$data->portfolio_argentina_max/100);

        $exposurenet_child_values_value79->setAttributeNode($exposurenet_child_values_value79_attr_name);

        $exposurenet_child_values_value79->setAttributeNode($exposurenet_child_values_value79_attr_ischecked);

        $exposurenet_child_values_value79->setAttributeNode($exposurenet_child_values_value79_attr_min);

        $exposurenet_child_values_value79->setAttributeNode($exposurenet_child_values_value79_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value79); 


        $exposurenet_child_values_value80 = $dom->createElement('Value');

        $exposurenet_child_values_value80_attr_name = new \DOMAttr('Name','Armenia');

        if($data->portfolio_armenia_status == "1")
        {
        $exposurenet_child_values_value80_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value80_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value80_attr_min = new \DOMAttr('Min',$data->portfolio_armenia_min/100);

        $exposurenet_child_values_value80_attr_max = new \DOMAttr('Max',$data->portfolio_armenia_max/100);

        $exposurenet_child_values_value80->setAttributeNode($exposurenet_child_values_value80_attr_name);

        $exposurenet_child_values_value80->setAttributeNode($exposurenet_child_values_value80_attr_ischecked);

        $exposurenet_child_values_value80->setAttributeNode($exposurenet_child_values_value80_attr_min);

        $exposurenet_child_values_value80->setAttributeNode($exposurenet_child_values_value80_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value80); 



        $exposurenet_child_values_value81 = $dom->createElement('Value');

        $exposurenet_child_values_value81_attr_name = new \DOMAttr('Name','Bahrain');

        if($data->portfolio_bahrain_status == "1")
        {
        $exposurenet_child_values_value81_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value81_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value81_attr_min = new \DOMAttr('Min',$data->portfolio_bahrain_min/100);

        $exposurenet_child_values_value81_attr_max = new \DOMAttr('Max',$data->portfolio_bahrain_max/100);

        $exposurenet_child_values_value81->setAttributeNode($exposurenet_child_values_value81_attr_name);

        $exposurenet_child_values_value81->setAttributeNode($exposurenet_child_values_value81_attr_ischecked);

        $exposurenet_child_values_value81->setAttributeNode($exposurenet_child_values_value81_attr_min);

        $exposurenet_child_values_value81->setAttributeNode($exposurenet_child_values_value81_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value81); 



        $exposurenet_child_values_value82 = $dom->createElement('Value');

        $exposurenet_child_values_value82_attr_name = new \DOMAttr('Name','Bermuda');

        if($data->portfolio_bermuda_status == "1")
        {
        $exposurenet_child_values_value82_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value82_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value82_attr_min = new \DOMAttr('Min',$data->portfolio_bermuda_min/100);

        $exposurenet_child_values_value82_attr_max = new \DOMAttr('Max',$data->portfolio_bermuda_max/100);

        $exposurenet_child_values_value82->setAttributeNode($exposurenet_child_values_value82_attr_name);

        $exposurenet_child_values_value82->setAttributeNode($exposurenet_child_values_value82_attr_ischecked);

        $exposurenet_child_values_value82->setAttributeNode($exposurenet_child_values_value82_attr_min);

        $exposurenet_child_values_value82->setAttributeNode($exposurenet_child_values_value82_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value82); 



        $exposurenet_child_values_value83 = $dom->createElement('Value');

        $exposurenet_child_values_value83_attr_name = new \DOMAttr('Name','Bolivia');

        if($data->portfolio_bolivia_status == "1")
        {
        $exposurenet_child_values_value83_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value83_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value83_attr_min = new \DOMAttr('Min',$data->portfolio_bolivia_min/100);

        $exposurenet_child_values_value83_attr_max = new \DOMAttr('Max',$data->portfolio_bolivia_max/100);

        $exposurenet_child_values_value83->setAttributeNode($exposurenet_child_values_value83_attr_name);

        $exposurenet_child_values_value83->setAttributeNode($exposurenet_child_values_value83_attr_ischecked);

        $exposurenet_child_values_value83->setAttributeNode($exposurenet_child_values_value83_attr_min);

        $exposurenet_child_values_value83->setAttributeNode($exposurenet_child_values_value83_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value83); 



        $exposurenet_child_values_value84 = $dom->createElement('Value');

        $exposurenet_child_values_value84_attr_name = new \DOMAttr('Name','Brazil');

        if($data->portfolio_brazil_status == "1")
        {
        $exposurenet_child_values_value84_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value84_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value84_attr_min = new \DOMAttr('Min',$data->portfolio_brazil_min/100);

        $exposurenet_child_values_value84_attr_max = new \DOMAttr('Max',$data->portfolio_brazil_max/100);

        $exposurenet_child_values_value84->setAttributeNode($exposurenet_child_values_value84_attr_name);

        $exposurenet_child_values_value84->setAttributeNode($exposurenet_child_values_value84_attr_ischecked);

        $exposurenet_child_values_value84->setAttributeNode($exposurenet_child_values_value84_attr_min);

        $exposurenet_child_values_value84->setAttributeNode($exposurenet_child_values_value84_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value84); 


        $exposurenet_child_values_value85 = $dom->createElement('Value');

        $exposurenet_child_values_value85_attr_name = new \DOMAttr('Name','British Virgin Islands');

        if($data->portfolio_british_virgin_islands_status == "1")
        {
        $exposurenet_child_values_value85_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value85_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value85_attr_min = new \DOMAttr('Min',$data->portfolio_british_virgin_islands_min/100);

        $exposurenet_child_values_value85_attr_max = new \DOMAttr('Max',$data->portfolio_british_virgin_islands_max/100);

        $exposurenet_child_values_value85->setAttributeNode($exposurenet_child_values_value85_attr_name);

        $exposurenet_child_values_value85->setAttributeNode($exposurenet_child_values_value85_attr_ischecked);

        $exposurenet_child_values_value85->setAttributeNode($exposurenet_child_values_value85_attr_min);

        $exposurenet_child_values_value85->setAttributeNode($exposurenet_child_values_value85_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value85); 


        $exposurenet_child_values_value86 = $dom->createElement('Value');

        $exposurenet_child_values_value86_attr_name = new \DOMAttr('Name','Cayman Islands');

        if($data->portfolio_cayman_islands_status == "1")
        {
        $exposurenet_child_values_value86_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value86_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value86_attr_min = new \DOMAttr('Min',$data->portfolio_cayman_islands_min/100);

        $exposurenet_child_values_value86_attr_max = new \DOMAttr('Max',$data->portfolio_cayman_islands_max/100);

        $exposurenet_child_values_value86->setAttributeNode($exposurenet_child_values_value86_attr_name);

        $exposurenet_child_values_value86->setAttributeNode($exposurenet_child_values_value86_attr_ischecked);

        $exposurenet_child_values_value86->setAttributeNode($exposurenet_child_values_value86_attr_min);

        $exposurenet_child_values_value86->setAttributeNode($exposurenet_child_values_value86_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value86); 



        $exposurenet_child_values_value87 = $dom->createElement('Value');

        $exposurenet_child_values_value87_attr_name = new \DOMAttr('Name','Chile');

        if($data->portfolio_chile_status == "1")
        {
        $exposurenet_child_values_value87_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value87_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value87_attr_min = new \DOMAttr('Min',$data->portfolio_chile_min/100);

        $exposurenet_child_values_value87_attr_max = new \DOMAttr('Max',$data->portfolio_chile_max/100);

        $exposurenet_child_values_value87->setAttributeNode($exposurenet_child_values_value87_attr_name);

        $exposurenet_child_values_value87->setAttributeNode($exposurenet_child_values_value87_attr_ischecked);

        $exposurenet_child_values_value87->setAttributeNode($exposurenet_child_values_value87_attr_min);

        $exposurenet_child_values_value87->setAttributeNode($exposurenet_child_values_value87_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value87); 



        $exposurenet_child_values_value88 = $dom->createElement('Value');

        $exposurenet_child_values_value88_attr_name = new \DOMAttr('Name','Colombia');

        if($data->portfolio_colombia_status == "1")
        {
        $exposurenet_child_values_value88_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value88_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value88_attr_min = new \DOMAttr('Min',$data->portfolio_colombia_min/100);

        $exposurenet_child_values_value88_attr_max = new \DOMAttr('Max',$data->portfolio_colombia_max/100);

        $exposurenet_child_values_value88->setAttributeNode($exposurenet_child_values_value88_attr_name);

        $exposurenet_child_values_value88->setAttributeNode($exposurenet_child_values_value88_attr_ischecked);

        $exposurenet_child_values_value88->setAttributeNode($exposurenet_child_values_value88_attr_min);

        $exposurenet_child_values_value88->setAttributeNode($exposurenet_child_values_value88_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value88); 



        $exposurenet_child_values_value89 = $dom->createElement('Value');

        $exposurenet_child_values_value89_attr_name = new \DOMAttr('Name','Congo');

        if($data->portfolio_congo_status == "1")
        {
        $exposurenet_child_values_value89_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value89_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value89_attr_min = new \DOMAttr('Min',$data->portfolio_congo_min/100);

        $exposurenet_child_values_value89_attr_max = new \DOMAttr('Max',$data->portfolio_congo_max/100);

        $exposurenet_child_values_value89->setAttributeNode($exposurenet_child_values_value89_attr_name);

        $exposurenet_child_values_value89->setAttributeNode($exposurenet_child_values_value89_attr_ischecked);

        $exposurenet_child_values_value89->setAttributeNode($exposurenet_child_values_value89_attr_min);

        $exposurenet_child_values_value89->setAttributeNode($exposurenet_child_values_value89_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value89); 



        $exposurenet_child_values_value90 = $dom->createElement('Value');

        $exposurenet_child_values_value90_attr_name = new \DOMAttr('Name','Costa Rica');

         if($data->portfolio_costa_rica_status == "1")
        {
        $exposurenet_child_values_value90_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value90_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value90_attr_min = new \DOMAttr('Min',$data->portfolio_costa_rica_min/100);

        $exposurenet_child_values_value90_attr_max = new \DOMAttr('Max',$data->portfolio_costa_rica_max/100);


        $exposurenet_child_values_value90->setAttributeNode($exposurenet_child_values_value90_attr_name);

        $exposurenet_child_values_value90->setAttributeNode($exposurenet_child_values_value90_attr_ischecked);

        $exposurenet_child_values_value90->setAttributeNode($exposurenet_child_values_value90_attr_min);

        $exposurenet_child_values_value90->setAttributeNode($exposurenet_child_values_value90_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value90); 


        $exposurenet_child_values_value91 = $dom->createElement('Value');

        $exposurenet_child_values_value91_attr_name = new \DOMAttr('Name','Côte dIvoire');

        if($data->portfolio_cote_dIvoire_status == "1")
        {
        $exposurenet_child_values_value91_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value91_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value91_attr_min = new \DOMAttr('Min',$data->portfolio_cote_dIvoire_min/100);

        $exposurenet_child_values_value91_attr_max = new \DOMAttr('Max',$data->portfolio_cote_dIvoire_min/100);

        $exposurenet_child_values_value91->setAttributeNode($exposurenet_child_values_value91_attr_name);

        $exposurenet_child_values_value91->setAttributeNode($exposurenet_child_values_value91_attr_ischecked);

        $exposurenet_child_values_value91->setAttributeNode($exposurenet_child_values_value91_attr_min);

        $exposurenet_child_values_value91->setAttributeNode($exposurenet_child_values_value91_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value91); 


        $exposurenet_child_values_value92 = $dom->createElement('Value');

        $exposurenet_child_values_value92_attr_name = new \DOMAttr('Name','Cuba');
       
        if($data->portfolio_cuba_status == "1")
        {
        $exposurenet_child_values_value92_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value92_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value92_attr_min = new \DOMAttr('Min',$data->portfolio_cuba_min/100);

        $exposurenet_child_values_value92_attr_max = new \DOMAttr('Max',$data->portfolio_cuba_max/100);


        $exposurenet_child_values_value92->setAttributeNode($exposurenet_child_values_value92_attr_name);

        $exposurenet_child_values_value92->setAttributeNode($exposurenet_child_values_value92_attr_ischecked);

        $exposurenet_child_values_value92->setAttributeNode($exposurenet_child_values_value92_attr_min);

        $exposurenet_child_values_value92->setAttributeNode($exposurenet_child_values_value92_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value92); 



        $exposurenet_child_values_value93 = $dom->createElement('Value');

        $exposurenet_child_values_value93_attr_name = new \DOMAttr('Name','Cyprus');

        if($data->portfolio_cyprus_status == "1")
        {
        $exposurenet_child_values_value93_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value93_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value93_attr_min = new \DOMAttr('Min',$data->portfolio_cyprus_min/100);

        $exposurenet_child_values_value93_attr_max = new \DOMAttr('Max',$data->portfolio_cyprus_max/100);


        $exposurenet_child_values_value93->setAttributeNode($exposurenet_child_values_value93_attr_name);

        $exposurenet_child_values_value93->setAttributeNode($exposurenet_child_values_value93_attr_ischecked);

        $exposurenet_child_values_value93->setAttributeNode($exposurenet_child_values_value93_attr_min);

        $exposurenet_child_values_value93->setAttributeNode($exposurenet_child_values_value93_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value93); 



        $exposurenet_child_values_value94 = $dom->createElement('Value');

        $exposurenet_child_values_value94_attr_name = new \DOMAttr('Name','Ecuador');

        if($data->portfolio_ecuador_status == "1")
        {
        $exposurenet_child_values_value94_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value94_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value94_attr_min = new \DOMAttr('Min',$data->portfolio_ecuador_min/100);

        $exposurenet_child_values_value94_attr_max = new \DOMAttr('Max',$data->portfolio_ecuador_max/100);

        $exposurenet_child_values_value94->setAttributeNode($exposurenet_child_values_value94_attr_name);

        $exposurenet_child_values_value94->setAttributeNode($exposurenet_child_values_value94_attr_ischecked);

        $exposurenet_child_values_value94->setAttributeNode($exposurenet_child_values_value94_attr_min);

        $exposurenet_child_values_value94->setAttributeNode($exposurenet_child_values_value94_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value94); 



        $exposurenet_child_values_value95 = $dom->createElement('Value');

        $exposurenet_child_values_value95_attr_name = new \DOMAttr('Name','Egypt');

         if($data->portfolio_egypt_status == "1")
        {
        $exposurenet_child_values_value95_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value95_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value95_attr_min = new \DOMAttr('Min',$data->portfolio_egypt_min/100);

        $exposurenet_child_values_value95_attr_max = new \DOMAttr('Max',$data->portfolio_egypt_max/100);

        $exposurenet_child_values_value95->setAttributeNode($exposurenet_child_values_value95_attr_name);

        $exposurenet_child_values_value95->setAttributeNode($exposurenet_child_values_value95_attr_ischecked);

        $exposurenet_child_values_value95->setAttributeNode($exposurenet_child_values_value95_attr_min);

        $exposurenet_child_values_value95->setAttributeNode($exposurenet_child_values_value95_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value95); 



        $exposurenet_child_values_value96 = $dom->createElement('Value');

        $exposurenet_child_values_value96_attr_name = new \DOMAttr('Name','El Salvador');

        if($data->portfolio_ei_salvador_status == "1")
        {
        $exposurenet_child_values_value96_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value96_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value96_attr_min = new \DOMAttr('Min',$data->portfolio_ei_salvador_min/100);

        $exposurenet_child_values_value96_attr_max = new \DOMAttr('Max',$data->portfolio_ei_salvador_max/100);


        $exposurenet_child_values_value96->setAttributeNode($exposurenet_child_values_value96_attr_name);

        $exposurenet_child_values_value96->setAttributeNode($exposurenet_child_values_value96_attr_ischecked);

        $exposurenet_child_values_value96->setAttributeNode($exposurenet_child_values_value96_attr_min);

        $exposurenet_child_values_value96->setAttributeNode($exposurenet_child_values_value96_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value96); 


        $exposurenet_child_values_value97 = $dom->createElement('Value');

        $exposurenet_child_values_value97_attr_name = new \DOMAttr('Name','Fiji');

        if($data->portfolio_fiji_status == "1")
        {
        $exposurenet_child_values_value97_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value97_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value97_attr_min = new \DOMAttr('Min',$data->portfolio_fiji_min/100);

        $exposurenet_child_values_value97_attr_max = new \DOMAttr('Max',$data->portfolio_fiji_max/100);

        $exposurenet_child_values_value97->setAttributeNode($exposurenet_child_values_value97_attr_name);

        $exposurenet_child_values_value97->setAttributeNode($exposurenet_child_values_value97_attr_ischecked);

        $exposurenet_child_values_value97->setAttributeNode($exposurenet_child_values_value97_attr_min);

        $exposurenet_child_values_value97->setAttributeNode($exposurenet_child_values_value97_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value97); 


        $exposurenet_child_values_value98 = $dom->createElement('Value');

        $exposurenet_child_values_value98_attr_name = new \DOMAttr('Name','Georgia');

         if($data->portfolio_georgia_status == "1")
        {
        $exposurenet_child_values_value98_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value98_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value98_attr_min = new \DOMAttr('Min',$data->portfolio_georgia_min/100);

        $exposurenet_child_values_value98_attr_max = new \DOMAttr('Max',$data->portfolio_georgia_max/100);

        $exposurenet_child_values_value98->setAttributeNode($exposurenet_child_values_value98_attr_name);

        $exposurenet_child_values_value98->setAttributeNode($exposurenet_child_values_value98_attr_ischecked);

        $exposurenet_child_values_value98->setAttributeNode($exposurenet_child_values_value98_attr_min);

        $exposurenet_child_values_value98->setAttributeNode($exposurenet_child_values_value98_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value98); 



        $exposurenet_child_values_value99 = $dom->createElement('Value');

        $exposurenet_child_values_value99_attr_name = new \DOMAttr('Name','Ghana');

         if($data->portfolio_ghana_status == "1")
        {
        $exposurenet_child_values_value99_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value99_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value99_attr_min = new \DOMAttr('Min',$data->portfolio_ghana_min/100);

        $exposurenet_child_values_value99_attr_max = new \DOMAttr('Max',$data->portfolio_ghana_max/100);

        $exposurenet_child_values_value99->setAttributeNode($exposurenet_child_values_value99_attr_name);

        $exposurenet_child_values_value99->setAttributeNode($exposurenet_child_values_value99_attr_ischecked);

        $exposurenet_child_values_value99->setAttributeNode($exposurenet_child_values_value99_attr_min);

        $exposurenet_child_values_value99->setAttributeNode($exposurenet_child_values_value99_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value99); 



        $exposurenet_child_values_value100 = $dom->createElement('Value');

        $exposurenet_child_values_value100_attr_name = new \DOMAttr('Name','Greenland');

        if($data->portfolio_greenland_status == "1")
        {
        $exposurenet_child_values_value100_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value100_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value100_attr_min = new \DOMAttr('Min',$data->portfolio_greenland_min/100);

        $exposurenet_child_values_value100_attr_max = new \DOMAttr('Max',$data->portfolio_greenland_max/100);

        $exposurenet_child_values_value100->setAttributeNode($exposurenet_child_values_value100_attr_name);

        $exposurenet_child_values_value100->setAttributeNode($exposurenet_child_values_value100_attr_ischecked);

        $exposurenet_child_values_value100->setAttributeNode($exposurenet_child_values_value100_attr_min);

        $exposurenet_child_values_value100->setAttributeNode($exposurenet_child_values_value100_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value100); 



        $exposurenet_child_values_value101 = $dom->createElement('Value');

        $exposurenet_child_values_value101_attr_name = new \DOMAttr('Name','Guam');

        if($data->portfolio_guam_status == "1")
        {
        $exposurenet_child_values_value101_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value101_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value101_attr_min = new \DOMAttr('Min',$data->portfolio_guam_min/100);

        $exposurenet_child_values_value101_attr_max = new \DOMAttr('Max',$data->portfolio_guam_max/100);

        $exposurenet_child_values_value101->setAttributeNode($exposurenet_child_values_value101_attr_name);

        $exposurenet_child_values_value101->setAttributeNode($exposurenet_child_values_value101_attr_ischecked);

        $exposurenet_child_values_value101->setAttributeNode($exposurenet_child_values_value101_attr_min);

        $exposurenet_child_values_value101->setAttributeNode($exposurenet_child_values_value101_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value101); 



        $exposurenet_child_values_value102 = $dom->createElement('Value');

        $exposurenet_child_values_value102_attr_name = new \DOMAttr('Name','Guatemala');

        if($data->portfolio_guatemala_status == "1")
        {
        $exposurenet_child_values_value102_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value102_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value102_attr_min = new \DOMAttr('Min',$data->portfolio_guatemala_min/100);

        $exposurenet_child_values_value102_attr_max = new \DOMAttr('Max',$data->portfolio_guatemala_max/100);

        $exposurenet_child_values_value102->setAttributeNode($exposurenet_child_values_value102_attr_name);

        $exposurenet_child_values_value102->setAttributeNode($exposurenet_child_values_value102_attr_ischecked);

        $exposurenet_child_values_value102->setAttributeNode($exposurenet_child_values_value102_attr_min);

        $exposurenet_child_values_value102->setAttributeNode($exposurenet_child_values_value102_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value102); 


        $exposurenet_child_values_value103 = $dom->createElement('Value');

        $exposurenet_child_values_value103_attr_name = new \DOMAttr('Name','Guinea');

        if($data->portfolio_guinea_status == "1")
        {
        $exposurenet_child_values_value103_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value103_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value103_attr_min = new \DOMAttr('Min',$data->portfolio_guinea_min/100);

        $exposurenet_child_values_value103_attr_max = new \DOMAttr('Max',$data->portfolio_guinea_max/100);

        $exposurenet_child_values_value103->setAttributeNode($exposurenet_child_values_value103_attr_name);

        $exposurenet_child_values_value103->setAttributeNode($exposurenet_child_values_value103_attr_ischecked);

        $exposurenet_child_values_value103->setAttributeNode($exposurenet_child_values_value103_attr_min);

        $exposurenet_child_values_value103->setAttributeNode($exposurenet_child_values_value103_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value103); 


        $exposurenet_child_values_value104 = $dom->createElement('Value');

        $exposurenet_child_values_value104_attr_name = new \DOMAttr('Name','Iraq');

        if($data->portfolio_iraq_status == "1")
        {
        $exposurenet_child_values_value104_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value104_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value104_attr_min = new \DOMAttr('Min',$data->portfolio_iraq_min/100);

        $exposurenet_child_values_value104_attr_max = new \DOMAttr('Max',$data->portfolio_iraq_max/100);


        $exposurenet_child_values_value104->setAttributeNode($exposurenet_child_values_value104_attr_name);

        $exposurenet_child_values_value104->setAttributeNode($exposurenet_child_values_value104_attr_ischecked);

        $exposurenet_child_values_value104->setAttributeNode($exposurenet_child_values_value104_attr_min);

        $exposurenet_child_values_value104->setAttributeNode($exposurenet_child_values_value104_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value104); 



        $exposurenet_child_values_value105 = $dom->createElement('Value');

        $exposurenet_child_values_value105_attr_name = new \DOMAttr('Name','Iran');

        if($data->portfolio_iran_status == "1")
        {
        $exposurenet_child_values_value105_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value105_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value105_attr_min = new \DOMAttr('Min',$data->portfolio_iran_min/100);

        $exposurenet_child_values_value105_attr_max = new \DOMAttr('Max',$data->portfolio_iran_max/100);

        $exposurenet_child_values_value105->setAttributeNode($exposurenet_child_values_value105_attr_name);

        $exposurenet_child_values_value105->setAttributeNode($exposurenet_child_values_value105_attr_ischecked);

        $exposurenet_child_values_value105->setAttributeNode($exposurenet_child_values_value105_attr_min);

        $exposurenet_child_values_value105->setAttributeNode($exposurenet_child_values_value105_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value105); 



        $exposurenet_child_values_value106 = $dom->createElement('Value');

        $exposurenet_child_values_value106_attr_name = new \DOMAttr('Name','Israel');

        if($data->portfolio_israel_status == "1")
        {
        $exposurenet_child_values_value106_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value106_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value106_attr_min = new \DOMAttr('Min',$data->portfolio_israel_min/100);

        $exposurenet_child_values_value106_attr_max = new \DOMAttr('Max',$data->portfolio_israel_max/100);


        $exposurenet_child_values_value106->setAttributeNode($exposurenet_child_values_value106_attr_name);

        $exposurenet_child_values_value106->setAttributeNode($exposurenet_child_values_value106_attr_ischecked);

        $exposurenet_child_values_value106->setAttributeNode($exposurenet_child_values_value106_attr_min);

        $exposurenet_child_values_value106->setAttributeNode($exposurenet_child_values_value106_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value106); 



        $exposurenet_child_values_value107 = $dom->createElement('Value');

        $exposurenet_child_values_value107_attr_name = new \DOMAttr('Name','Jamaica');

        if($data->portfolio_jamaica_status == "1")
        {
        $exposurenet_child_values_value107_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value107_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value107_attr_min = new \DOMAttr('Min',$data->portfolio_jamaica_min/100);

        $exposurenet_child_values_value107_attr_max = new \DOMAttr('Max',$data->portfolio_jamaica_max/100);

        $exposurenet_child_values_value107->setAttributeNode($exposurenet_child_values_value107_attr_name);

        $exposurenet_child_values_value107->setAttributeNode($exposurenet_child_values_value107_attr_ischecked);

        $exposurenet_child_values_value107->setAttributeNode($exposurenet_child_values_value107_attr_min);

        $exposurenet_child_values_value107->setAttributeNode($exposurenet_child_values_value107_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value107); 



        $exposurenet_child_values_value108 = $dom->createElement('Value');

        $exposurenet_child_values_value108_attr_name = new \DOMAttr('Name','Jordan');

        if($data->portfolio_jordan_status == "1")
        {
        $exposurenet_child_values_value108_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value108_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value108_attr_min = new \DOMAttr('Min',$data->portfolio_jordan_min/100);

        $exposurenet_child_values_value108_attr_max = new \DOMAttr('Max',$data->portfolio_jordan_max/100);

        $exposurenet_child_values_value108->setAttributeNode($exposurenet_child_values_value108_attr_name);

        $exposurenet_child_values_value108->setAttributeNode($exposurenet_child_values_value108_attr_ischecked);

        $exposurenet_child_values_value108->setAttributeNode($exposurenet_child_values_value108_attr_min);

        $exposurenet_child_values_value108->setAttributeNode($exposurenet_child_values_value108_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value108); 


        $exposurenet_child_values_value109 = $dom->createElement('Value');

        $exposurenet_child_values_value109_attr_name = new \DOMAttr('Name','Kenya');

        if($data->portfolio_kenya_status == "1")
        {
        $exposurenet_child_values_value109_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value109_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value109_attr_min = new \DOMAttr('Min',$data->portfolio_kenya_min/100);

        $exposurenet_child_values_value109_attr_max = new \DOMAttr('Max',$data->portfolio_kenya_max/100);

        $exposurenet_child_values_value109->setAttributeNode($exposurenet_child_values_value109_attr_name);

        $exposurenet_child_values_value109->setAttributeNode($exposurenet_child_values_value109_attr_ischecked);

        $exposurenet_child_values_value109->setAttributeNode($exposurenet_child_values_value109_attr_min);

        $exposurenet_child_values_value109->setAttributeNode($exposurenet_child_values_value109_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value109); 


        $exposurenet_child_values_value110 = $dom->createElement('Value');

        $exposurenet_child_values_value110_attr_name = new \DOMAttr('Name','Kuwait');

         if($data->portfolio_kuwait_status == "1")
        {
        $exposurenet_child_values_value110_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value110_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value110_attr_min = new \DOMAttr('Min',$data->portfolio_kuwait_min/100);

        $exposurenet_child_values_value110_attr_max = new \DOMAttr('Max',$data->portfolio_kuwait_max/100);

        $exposurenet_child_values_value110->setAttributeNode($exposurenet_child_values_value110_attr_name);

        $exposurenet_child_values_value110->setAttributeNode($exposurenet_child_values_value110_attr_ischecked);

        $exposurenet_child_values_value110->setAttributeNode($exposurenet_child_values_value110_attr_min);

        $exposurenet_child_values_value110->setAttributeNode($exposurenet_child_values_value110_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value110); 



        $exposurenet_child_values_value111 = $dom->createElement('Value');

        $exposurenet_child_values_value111_attr_name = new \DOMAttr('Name','Lebanon');

        if($data->portfolio_lebanon_status == "1")
        {
        $exposurenet_child_values_value111_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value111_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value111_attr_min = new \DOMAttr('Min',$data->portfolio_lebanon_min/100);

        $exposurenet_child_values_value111_attr_max = new \DOMAttr('Max',$data->portfolio_lebanon_max/100);

        $exposurenet_child_values_value111->setAttributeNode($exposurenet_child_values_value111_attr_name);

        $exposurenet_child_values_value111->setAttributeNode($exposurenet_child_values_value111_attr_ischecked);

        $exposurenet_child_values_value111->setAttributeNode($exposurenet_child_values_value111_attr_min);

        $exposurenet_child_values_value111->setAttributeNode($exposurenet_child_values_value111_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value111); 


        $exposurenet_child_values_value112 = $dom->createElement('Value');

        $exposurenet_child_values_value112_attr_name = new \DOMAttr('Name','Libya');

        if($data->portfolio_libya_status == "1")
        {
        $exposurenet_child_values_value112_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value112_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value112_attr_min = new \DOMAttr('Min',$data->portfolio_libya_min/100);

        $exposurenet_child_values_value112_attr_max = new \DOMAttr('Max',$data->portfolio_libya_max/100);

        $exposurenet_child_values_value112->setAttributeNode($exposurenet_child_values_value112_attr_name);

        $exposurenet_child_values_value112->setAttributeNode($exposurenet_child_values_value112_attr_ischecked);

        $exposurenet_child_values_value112->setAttributeNode($exposurenet_child_values_value112_attr_min);

        $exposurenet_child_values_value112->setAttributeNode($exposurenet_child_values_value112_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value112); 


        $exposurenet_child_values_value113 = $dom->createElement('Value');

        $exposurenet_child_values_value113_attr_name = new \DOMAttr('Name','Madagascar');

        if($data->portfolio_madagascar_status == "1")
        {
        $exposurenet_child_values_value113_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value113_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value113_attr_min = new \DOMAttr('Min',$data->portfolio_madagascar_min/100);

        $exposurenet_child_values_value113_attr_max = new \DOMAttr('Max',$data->portfolio_madagascar_max/100);

        $exposurenet_child_values_value113->setAttributeNode($exposurenet_child_values_value113_attr_name);

        $exposurenet_child_values_value113->setAttributeNode($exposurenet_child_values_value113_attr_ischecked);

        $exposurenet_child_values_value113->setAttributeNode($exposurenet_child_values_value113_attr_min);

        $exposurenet_child_values_value113->setAttributeNode($exposurenet_child_values_value113_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value113); 



        $exposurenet_child_values_value114 = $dom->createElement('Value');

        $exposurenet_child_values_value114_attr_name = new \DOMAttr('Name','Mauritius');

        if($data->portfolio_mauritius_status == "1")
        {
        $exposurenet_child_values_value114_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value114_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value114_attr_min = new \DOMAttr('Min',$data->portfolio_mauritius_min/100);

        $exposurenet_child_values_value114_attr_max = new \DOMAttr('Max',$data->portfolio_mauritius_max/100);

        $exposurenet_child_values_value114->setAttributeNode($exposurenet_child_values_value114_attr_name);

        $exposurenet_child_values_value114->setAttributeNode($exposurenet_child_values_value114_attr_ischecked);

        $exposurenet_child_values_value114->setAttributeNode($exposurenet_child_values_value114_attr_min);

        $exposurenet_child_values_value114->setAttributeNode($exposurenet_child_values_value114_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value114); 


        $exposurenet_child_values_value115 = $dom->createElement('Value');

        $exposurenet_child_values_value115_attr_name = new \DOMAttr('Name','Mexico');

        if($data->portfolio_mexico_status == "1")
        {
        $exposurenet_child_values_value115_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value115_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value115_attr_min = new \DOMAttr('Min',$data->portfolio_mexico_min/100);

        $exposurenet_child_values_value115_attr_max = new \DOMAttr('Max',$data->portfolio_mexico_max/100);

        $exposurenet_child_values_value115->setAttributeNode($exposurenet_child_values_value115_attr_name);

        $exposurenet_child_values_value115->setAttributeNode($exposurenet_child_values_value115_attr_ischecked);

        $exposurenet_child_values_value115->setAttributeNode($exposurenet_child_values_value115_attr_min);

        $exposurenet_child_values_value115->setAttributeNode($exposurenet_child_values_value115_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value115); 


        $exposurenet_child_values_value116 = $dom->createElement('Value');

        $exposurenet_child_values_value116_attr_name = new \DOMAttr('Name','Morocco');

        if($data->portfolio_morocco_status == "1")
        {
        $exposurenet_child_values_value116_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value116_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value116_attr_min = new \DOMAttr('Min',$data->portfolio_morocco_min/100);

        $exposurenet_child_values_value116_attr_max = new \DOMAttr('Max',$data->portfolio_morocco_max/100);

        $exposurenet_child_values_value116->setAttributeNode($exposurenet_child_values_value116_attr_name);

        $exposurenet_child_values_value116->setAttributeNode($exposurenet_child_values_value116_attr_ischecked);

        $exposurenet_child_values_value116->setAttributeNode($exposurenet_child_values_value116_attr_min);

        $exposurenet_child_values_value116->setAttributeNode($exposurenet_child_values_value116_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value116); 



        $exposurenet_child_values_value117 = $dom->createElement('Value');

        $exposurenet_child_values_value117_attr_name = new \DOMAttr('Name','Nigeria');

         if($data->portfolio_nigeria_status == "1")
        {
        $exposurenet_child_values_value117_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value117_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value117_attr_min = new \DOMAttr('Min',$data->portfolio_nigeria_min/100);

        $exposurenet_child_values_value117_attr_max = new \DOMAttr('Max',$data->portfolio_nigeria_max/100);

        $exposurenet_child_values_value117->setAttributeNode($exposurenet_child_values_value117_attr_name);

        $exposurenet_child_values_value117->setAttributeNode($exposurenet_child_values_value117_attr_ischecked);

        $exposurenet_child_values_value117->setAttributeNode($exposurenet_child_values_value117_attr_min);

        $exposurenet_child_values_value117->setAttributeNode($exposurenet_child_values_value117_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value117); 



        $exposurenet_child_values_value118 = $dom->createElement('Value');

        $exposurenet_child_values_value118_attr_name = new \DOMAttr('Name','Oman');

         if($data->portfolio_oman_status == "1")
        {
        $exposurenet_child_values_value118_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value118_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value118_attr_min = new \DOMAttr('Min',$data->portfolio_oman_min/100);

        $exposurenet_child_values_value118_attr_max = new \DOMAttr('Max',$data->portfolio_oman_max/100);

        $exposurenet_child_values_value118->setAttributeNode($exposurenet_child_values_value118_attr_name);

        $exposurenet_child_values_value118->setAttributeNode($exposurenet_child_values_value118_attr_ischecked);

        $exposurenet_child_values_value118->setAttributeNode($exposurenet_child_values_value118_attr_min);

        $exposurenet_child_values_value118->setAttributeNode($exposurenet_child_values_value118_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value118); 



        $exposurenet_child_values_value119 = $dom->createElement('Value');

        $exposurenet_child_values_value119_attr_name = new \DOMAttr('Name','Palestine');

        if($data->portfolio_palestine_status == "1")
        {
        $exposurenet_child_values_value119_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value119_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value119_attr_min = new \DOMAttr('Min',$data->portfolio_palestine_min/100);

        $exposurenet_child_values_value119_attr_max = new \DOMAttr('Max',$data->portfolio_palestine_max/100);

        $exposurenet_child_values_value119->setAttributeNode($exposurenet_child_values_value119_attr_name);

        $exposurenet_child_values_value119->setAttributeNode($exposurenet_child_values_value119_attr_ischecked);

        $exposurenet_child_values_value119->setAttributeNode($exposurenet_child_values_value119_attr_min);

        $exposurenet_child_values_value119->setAttributeNode($exposurenet_child_values_value119_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value119); 



        $exposurenet_child_values_value120 = $dom->createElement('Value');

        $exposurenet_child_values_value120_attr_name = new \DOMAttr('Name','Peru');

       if($data->portfolio_peru_status == "1")
        {
        $exposurenet_child_values_value120_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value120_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value120_attr_min = new \DOMAttr('Min',$data->portfolio_peru_min/100);

        $exposurenet_child_values_value120_attr_max = new \DOMAttr('Max',$data->portfolio_peru_max/100);

        $exposurenet_child_values_value120->setAttributeNode($exposurenet_child_values_value120_attr_name);

        $exposurenet_child_values_value120->setAttributeNode($exposurenet_child_values_value120_attr_ischecked);

        $exposurenet_child_values_value120->setAttributeNode($exposurenet_child_values_value120_attr_min);

        $exposurenet_child_values_value120->setAttributeNode($exposurenet_child_values_value120_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value120); 


        $exposurenet_child_values_value121 = $dom->createElement('Value');

        $exposurenet_child_values_value121_attr_name = new \DOMAttr('Name','Panama');

       
       if($data->portfolio_panama_status == "1")
        {
        $exposurenet_child_values_value121_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value121_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value121_attr_min = new \DOMAttr('Min',$data->portfolio_panama_min/100);

        $exposurenet_child_values_value121_attr_max = new \DOMAttr('Max',$data->portfolio_panama_max/100);

        $exposurenet_child_values_value121->setAttributeNode($exposurenet_child_values_value121_attr_name);

        $exposurenet_child_values_value121->setAttributeNode($exposurenet_child_values_value121_attr_ischecked);

        $exposurenet_child_values_value121->setAttributeNode($exposurenet_child_values_value121_attr_min);

        $exposurenet_child_values_value121->setAttributeNode($exposurenet_child_values_value121_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value121); 


        $exposurenet_child_values_value122 = $dom->createElement('Value');

        $exposurenet_child_values_value122_attr_name = new \DOMAttr('Name','Papua New Guinea');

       if($data->portfolio_papua_new_guinea_status == "1")
        {
        $exposurenet_child_values_value122_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value122_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value122_attr_min = new \DOMAttr('Min',$data->portfolio_papua_new_guinea_min/100);

        $exposurenet_child_values_value122_attr_max = new \DOMAttr('Max',$data->portfolio_papua_new_guinea_max/100);

        $exposurenet_child_values_value122->setAttributeNode($exposurenet_child_values_value122_attr_name);

        $exposurenet_child_values_value122->setAttributeNode($exposurenet_child_values_value122_attr_ischecked);

        $exposurenet_child_values_value122->setAttributeNode($exposurenet_child_values_value122_attr_min);

        $exposurenet_child_values_value122->setAttributeNode($exposurenet_child_values_value122_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value122); 


        $exposurenet_child_values_value123 = $dom->createElement('Value');

        $exposurenet_child_values_value123_attr_name = new \DOMAttr('Name','Paraguay');

        if($data->portfolio_paraguay_status == "1")
        {
        $exposurenet_child_values_value123_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value123_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value123_attr_min = new \DOMAttr('Min',$data->portfolio_paraguay_min/100);

        $exposurenet_child_values_value123_attr_max = new \DOMAttr('Max',$data->portfolio_paraguay_max/100);

        $exposurenet_child_values_value123->setAttributeNode($exposurenet_child_values_value123_attr_name);

        $exposurenet_child_values_value123->setAttributeNode($exposurenet_child_values_value123_attr_ischecked);

        $exposurenet_child_values_value123->setAttributeNode($exposurenet_child_values_value123_attr_min);

        $exposurenet_child_values_value123->setAttributeNode($exposurenet_child_values_value123_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value123); 



        $exposurenet_child_values_value124 = $dom->createElement('Value');

        $exposurenet_child_values_value124_attr_name = new \DOMAttr('Name','Puerto Rico');

       if($data->portfolio_puerto_rico_status == "1")
        {
        $exposurenet_child_values_value124_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value124_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value124_attr_min = new \DOMAttr('Min',$data->portfolio_puerto_rico_min/100);

        $exposurenet_child_values_value124_attr_max = new \DOMAttr('Max',$data->portfolio_puerto_rico_max/100);

        $exposurenet_child_values_value124->setAttributeNode($exposurenet_child_values_value124_attr_name);

        $exposurenet_child_values_value124->setAttributeNode($exposurenet_child_values_value124_attr_ischecked);

        $exposurenet_child_values_value124->setAttributeNode($exposurenet_child_values_value124_attr_min);

        $exposurenet_child_values_value124->setAttributeNode($exposurenet_child_values_value124_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value124); 



        $exposurenet_child_values_value125 = $dom->createElement('Value');

        $exposurenet_child_values_value125_attr_name = new \DOMAttr('Name','Qatar');

        if($data->portfolio_qatar_status == "1")
        {
        $exposurenet_child_values_value125_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value125_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value125_attr_min = new \DOMAttr('Min',$data->portfolio_qatar_min/100);

        $exposurenet_child_values_value125_attr_max = new \DOMAttr('Max',$data->portfolio_qatar_max/100);

        $exposurenet_child_values_value125->setAttributeNode($exposurenet_child_values_value125_attr_name);

        $exposurenet_child_values_value125->setAttributeNode($exposurenet_child_values_value125_attr_ischecked);

        $exposurenet_child_values_value125->setAttributeNode($exposurenet_child_values_value125_attr_min);

        $exposurenet_child_values_value125->setAttributeNode($exposurenet_child_values_value125_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value125); 



        $exposurenet_child_values_value126 = $dom->createElement('Value');

        $exposurenet_child_values_value126_attr_name = new \DOMAttr('Name','Rwanda');

       if($data->portfolio_rwanda_status == "1")
        {
        $exposurenet_child_values_value126_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value126_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value126_attr_min = new \DOMAttr('Min',$data->portfolio_rwanda_min/100);

        $exposurenet_child_values_value126_attr_max = new \DOMAttr('Max',$data->portfolio_rwanda_max/100);

        $exposurenet_child_values_value126->setAttributeNode($exposurenet_child_values_value126_attr_name);

        $exposurenet_child_values_value126->setAttributeNode($exposurenet_child_values_value126_attr_ischecked);

        $exposurenet_child_values_value126->setAttributeNode($exposurenet_child_values_value126_attr_min);

        $exposurenet_child_values_value126->setAttributeNode($exposurenet_child_values_value126_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value126); 


        $exposurenet_child_values_value127 = $dom->createElement('Value');

        $exposurenet_child_values_value127_attr_name = new \DOMAttr('Name','Saudi Arabia');

        if($data->portfolio_saudi_arabia_status == "1")
        {
        $exposurenet_child_values_value127_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value127_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value127_attr_min = new \DOMAttr('Min',$data->portfolio_saudi_arabia_min/100);

        $exposurenet_child_values_value127_attr_max = new \DOMAttr('Max',$data->portfolio_saudi_arabia_max/100);


        $exposurenet_child_values_value127->setAttributeNode($exposurenet_child_values_value127_attr_name);

        $exposurenet_child_values_value127->setAttributeNode($exposurenet_child_values_value127_attr_ischecked);

        $exposurenet_child_values_value127->setAttributeNode($exposurenet_child_values_value127_attr_min);

        $exposurenet_child_values_value127->setAttributeNode($exposurenet_child_values_value127_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value127); 
  
       

        $exposurenet_child_values_value128 = $dom->createElement('Value');

        $exposurenet_child_values_value128_attr_name = new \DOMAttr('Name','South Africa');

        if($data->portfolio_south_africa_status == "1")
        {
        $exposurenet_child_values_value128_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value128_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value128_attr_min = new \DOMAttr('Min',$data->portfolio_south_africa_min/100);

        $exposurenet_child_values_value128_attr_max = new \DOMAttr('Max',$data->portfolio_south_africa_max/100);

        $exposurenet_child_values_value128->setAttributeNode($exposurenet_child_values_value128_attr_name);

        $exposurenet_child_values_value128->setAttributeNode($exposurenet_child_values_value128_attr_ischecked);

        $exposurenet_child_values_value128->setAttributeNode($exposurenet_child_values_value128_attr_min);

        $exposurenet_child_values_value128->setAttributeNode($exposurenet_child_values_value128_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value128); 
       


        $exposurenet_child_values_value129 = $dom->createElement('Value');

        $exposurenet_child_values_value129_attr_name = new \DOMAttr('Name','Togo');

        if($data->portfolio_togo_status == "1")
        {
        $exposurenet_child_values_value129_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value129_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value129_attr_min = new \DOMAttr('Min',$data->portfolio_togo_min/100);

        $exposurenet_child_values_value129_attr_max = new \DOMAttr('Max',$data->portfolio_togo_max/100);


        $exposurenet_child_values_value129->setAttributeNode($exposurenet_child_values_value129_attr_name);

        $exposurenet_child_values_value129->setAttributeNode($exposurenet_child_values_value129_attr_ischecked);

        $exposurenet_child_values_value129->setAttributeNode($exposurenet_child_values_value129_attr_min);

        $exposurenet_child_values_value129->setAttributeNode($exposurenet_child_values_value129_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value129); 



        $exposurenet_child_values_value130 = $dom->createElement('Value');

        $exposurenet_child_values_value130_attr_name = new \DOMAttr('Name','Turkey');

        if($data->portfolio_turkey_status == "1")
        {
        $exposurenet_child_values_value130_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value130_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value130_attr_min = new \DOMAttr('Min',$data->portfolio_turkey_min/100);

        $exposurenet_child_values_value130_attr_max = new \DOMAttr('Max',$data->portfolio_turkey_max/100);

        $exposurenet_child_values_value130->setAttributeNode($exposurenet_child_values_value130_attr_name);

        $exposurenet_child_values_value130->setAttributeNode($exposurenet_child_values_value130_attr_ischecked);

        $exposurenet_child_values_value130->setAttributeNode($exposurenet_child_values_value130_attr_min);

        $exposurenet_child_values_value130->setAttributeNode($exposurenet_child_values_value130_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value130); 



        $exposurenet_child_values_value131 = $dom->createElement('Value');

        $exposurenet_child_values_value131_attr_name = new \DOMAttr('Name','United Arab Emirates');

       if($data->portfolio_united_arab_emirates_status == "1")
        {
        $exposurenet_child_values_value131_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value131_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value131_attr_min = new \DOMAttr('Min',$data->portfolio_united_arab_emirates_min/100);

        $exposurenet_child_values_value131_attr_max = new \DOMAttr('Max',$data->portfolio_united_arab_emirates_max/100);

        $exposurenet_child_values_value131->setAttributeNode($exposurenet_child_values_value131_attr_name);

        $exposurenet_child_values_value131->setAttributeNode($exposurenet_child_values_value131_attr_ischecked);

        $exposurenet_child_values_value131->setAttributeNode($exposurenet_child_values_value131_attr_min);

        $exposurenet_child_values_value131->setAttributeNode($exposurenet_child_values_value131_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value131); 



        $exposurenet_child_values_value132 = $dom->createElement('Value');

        $exposurenet_child_values_value132_attr_name = new \DOMAttr('Name','Uruguay');

        if($data->portfolio_uruguay_status == "1")
        {
        $exposurenet_child_values_value132_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value132_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value132_attr_min = new \DOMAttr('Min',$data->portfolio_uruguay_min/100);

        $exposurenet_child_values_value132_attr_max = new \DOMAttr('Max',$data->portfolio_uruguay_max/100);

        $exposurenet_child_values_value132->setAttributeNode($exposurenet_child_values_value132_attr_name);

        $exposurenet_child_values_value132->setAttributeNode($exposurenet_child_values_value132_attr_ischecked);

        $exposurenet_child_values_value132->setAttributeNode($exposurenet_child_values_value132_attr_min);

        $exposurenet_child_values_value132->setAttributeNode($exposurenet_child_values_value132_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value132); 


        $exposurenet_child_values_value133 = $dom->createElement('Value');

        $exposurenet_child_values_value133_attr_name = new \DOMAttr('Name','Venezuela');

        if($data->portfolio_venezuela_status == "1")
        {
        $exposurenet_child_values_value133_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value133_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value133_attr_min = new \DOMAttr('Min',$data->portfolio_venezuela_min/100);

        $exposurenet_child_values_value133_attr_max = new \DOMAttr('Max',$data->portfolio_venezuela_max/100);

        $exposurenet_child_values_value133->setAttributeNode($exposurenet_child_values_value133_attr_name);

        $exposurenet_child_values_value133->setAttributeNode($exposurenet_child_values_value133_attr_ischecked);

        $exposurenet_child_values_value133->setAttributeNode($exposurenet_child_values_value133_attr_min);

        $exposurenet_child_values_value133->setAttributeNode($exposurenet_child_values_value133_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value133); 


        $exposurenet_child_values_value134 = $dom->createElement('Value');

        $exposurenet_child_values_value134_attr_name = new \DOMAttr('Name','Yemen');

       if($data->portfolio_yemen_status == "1")
        {
        $exposurenet_child_values_value134_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value134_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value134_attr_min = new \DOMAttr('Min',$data->portfolio_yemen_min/100);

        $exposurenet_child_values_value134_attr_max = new \DOMAttr('Max',$data->portfolio_yemen_max/100);

        $exposurenet_child_values_value134->setAttributeNode($exposurenet_child_values_value134_attr_name);

        $exposurenet_child_values_value134->setAttributeNode($exposurenet_child_values_value134_attr_ischecked);

        $exposurenet_child_values_value134->setAttributeNode($exposurenet_child_values_value134_attr_min);

        $exposurenet_child_values_value134->setAttributeNode($exposurenet_child_values_value134_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value134); 


        $exposurenet_child_values_value135 = $dom->createElement('Value');

        $exposurenet_child_values_value135_attr_name = new \DOMAttr('Name','Zambia');

          if($data->portfolio_zambia_status == "1")
        {
        $exposurenet_child_values_value135_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value135_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value135_attr_min = new \DOMAttr('Min',$data->portfolio_zambia_min/100);

        $exposurenet_child_values_value135_attr_max = new \DOMAttr('Max',$data->portfolio_zambia_max/100);

        $exposurenet_child_values_value135->setAttributeNode($exposurenet_child_values_value135_attr_name);

        $exposurenet_child_values_value135->setAttributeNode($exposurenet_child_values_value135_attr_ischecked);

        $exposurenet_child_values_value135->setAttributeNode($exposurenet_child_values_value135_attr_min);

        $exposurenet_child_values_value135->setAttributeNode($exposurenet_child_values_value135_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value135); 



        $exposurenet_child_values_value136 = $dom->createElement('Value');

        $exposurenet_child_values_value136_attr_name = new \DOMAttr('Name','Zimbabwe');

          if($data->portfolio_zimbabwe_status == "1")
        {
        $exposurenet_child_values_value136_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value136_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value136_attr_min = new \DOMAttr('Min',$data->portfolio_zimbabwe_min/100);

        $exposurenet_child_values_value136_attr_max = new \DOMAttr('Max',$data->portfolio_zimbabwe_max/100);

        $exposurenet_child_values_value136->setAttributeNode($exposurenet_child_values_value136_attr_name);

        $exposurenet_child_values_value136->setAttributeNode($exposurenet_child_values_value136_attr_ischecked);

        $exposurenet_child_values_value136->setAttributeNode($exposurenet_child_values_value136_attr_min);

        $exposurenet_child_values_value136->setAttributeNode($exposurenet_child_values_value136_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value136); 



        $exposurenet_child_values_value137 = $dom->createElement('Value');

        $exposurenet_child_values_value137_attr_name = new \DOMAttr('Name','Global');

       if($data->portfolio_global1_status == "1")
        {
        $exposurenet_child_values_value137_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value137_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value137_attr_min = new \DOMAttr('Min',$data->portfolio_global1_min/100);

        $exposurenet_child_values_value137_attr_max = new \DOMAttr('Max',$data->portfolio_global1_max/100);

        $exposurenet_child_values_value137->setAttributeNode($exposurenet_child_values_value137_attr_name);

        $exposurenet_child_values_value137->setAttributeNode($exposurenet_child_values_value137_attr_ischecked);

        $exposurenet_child_values_value137->setAttributeNode($exposurenet_child_values_value137_attr_min);

        $exposurenet_child_values_value137->setAttributeNode($exposurenet_child_values_value137_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value137); 


       
        $exposurenet_child_values_value162 = $dom->createElement('Value');

        $exposurenet_child_values_value162_attr_name = new \DOMAttr('Name','Global');

       if($data->portfolio_global2_status == "1")
        {
        $exposurenet_child_values_value162_attr_ischecked = new \DOMAttr('IsChecked','true');
        }
        else
        {
        $exposurenet_child_values_value162_attr_ischecked = new \DOMAttr('IsChecked','false');
        }

        $exposurenet_child_values_value162_attr_min = new \DOMAttr('Min',$data->portfolio_global2_min/100);

        $exposurenet_child_values_value162_attr_max = new \DOMAttr('Max',$data->portfolio_global2_max/100);


        $exposurenet_child_values_value162->setAttributeNode($exposurenet_child_values_value162_attr_name);

        $exposurenet_child_values_value162->setAttributeNode($exposurenet_child_values_value162_attr_ischecked);

        $exposurenet_child_values_value162->setAttributeNode($exposurenet_child_values_value162_attr_min);

        $exposurenet_child_values_value162->setAttributeNode($exposurenet_child_values_value162_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value162); 

   

        $exposurenet_child_values_value138 = $dom->createElement('Value');

        $exposurenet_child_values_value138_attr_name = new \DOMAttr('Name','APG Sector Exposure');

        $exposurenet_child_values_value138_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value138_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value138_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value138->setAttributeNode($exposurenet_child_values_value138_attr_name);

        $exposurenet_child_values_value138->setAttributeNode($exposurenet_child_values_value138_attr_ischecked);

        $exposurenet_child_values_value138->setAttributeNode($exposurenet_child_values_value138_attr_min);

        $exposurenet_child_values_value138->setAttributeNode($exposurenet_child_values_value138_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value138); 


        $exposurenet_child_values_value139 = $dom->createElement('Value');

        $exposurenet_child_values_value139_attr_name = new \DOMAttr('Name','Real Estate');

        if($data->portfolio_realestate_status == "1" && $data->portfolio_esc_realestate_status == "1")
        {
        $exposurenet_child_values_value139_attr_ischecked = new \DOMAttr('IsChecked','true');

        $realestate_min_val1 = $data->portfolio_esc_realestate_min/100*$data->portfolio_fixedincome_min/100;

        $realestate_min_val2 = $data->portfolio_realestate_min/100*$data->portfolio_equities_min/100;
       
        $realestate_tot_min_val = ($realestate_min_val1+$realestate_min_val2)*100;
       
        if($realestate_tot_min_val > 100)
        {
        $realestate_min = round($realestate_tot_min_val, -2);
        }
        else
        {
        $realestate_min = round($realestate_tot_min_val);
        }

        $realestate_max_val1 = $data->portfolio_esc_realestate_max/100*$data->portfolio_fixedincome_max/100;

        $realestate_max_val2 = $data->portfolio_realestate_max/100*$data->portfolio_equities_max/100;
       
        $realestate_tot_max_val = ($realestate_max_val1+$realestate_max_val2)*100;
       
        if($realestate_tot_max_val > 100)
        {
        $realestate_max = round($realestate_tot_max_val, -2);
        }
        else
        {
        $realestate_max = round($realestate_tot_max_val);
        }
               
        $exposurenet_child_values_value139_attr_min = new \DOMAttr('Min',$realestate_min/100);

        $exposurenet_child_values_value139_attr_max = new \DOMAttr('Max',$realestate_max/100);
        
        }
        else
        {
        $exposurenet_child_values_value139_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value139_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value139_attr_max = new \DOMAttr('Max',1);    
        }
        
        $exposurenet_child_values_value139->setAttributeNode($exposurenet_child_values_value139_attr_name);

        $exposurenet_child_values_value139->setAttributeNode($exposurenet_child_values_value139_attr_ischecked);

        $exposurenet_child_values_value139->setAttributeNode($exposurenet_child_values_value139_attr_min);

        $exposurenet_child_values_value139->setAttributeNode($exposurenet_child_values_value139_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value139); 


        $exposurenet_child_values_value140 = $dom->createElement('Value');

        $exposurenet_child_values_value140_attr_name = new \DOMAttr('Name','Technology');

        if($data->portfolio_it_status == "1" && $data->portfolio_esc_it_status == "1")
        {
        $exposurenet_child_values_value140_attr_ischecked = new \DOMAttr('IsChecked','true');

        $it_min_val1 = $data->portfolio_esc_it_min/100*$data->portfolio_fixedincome_min/100;

        $it_min_val2 = $data->portfolio_it_min/100*$data->portfolio_equities_min/100;
       
        $it_tot_min_val = ($it_min_val1+$it_min_val2)*100;
       
        if($it_tot_min_val > 100)
        {
        $it_min = round($it_tot_min_val, -2);
        }
        else
        {
        $it_min = round($it_tot_min_val);
        }

        $it_max_val1 = $data->portfolio_esc_it_max/100*$data->portfolio_fixedincome_max/100;

        $it_max_val2 = $data->portfolio_it_max/100*$data->portfolio_equities_max/100;
       
        $it_tot_max_val = ($it_max_val1+$it_max_val2)*100;
       
        if($it_tot_max_val > 100)
        {
        $it_max = round($it_tot_max_val, -2);
        }
        else
        {
        $it_max = round($it_tot_max_val);
        }
               
        $exposurenet_child_values_value140_attr_min = new \DOMAttr('Min',$it_min/100);

        $exposurenet_child_values_value140_attr_max = new \DOMAttr('Max',$it_max/100);

        }
        else
        {
        $exposurenet_child_values_value140_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value140_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value140_attr_max = new \DOMAttr('Max',1);

        }

        
        $exposurenet_child_values_value140->setAttributeNode($exposurenet_child_values_value140_attr_name);

        $exposurenet_child_values_value140->setAttributeNode($exposurenet_child_values_value140_attr_ischecked);

        $exposurenet_child_values_value140->setAttributeNode($exposurenet_child_values_value140_attr_min);

        $exposurenet_child_values_value140->setAttributeNode($exposurenet_child_values_value140_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value140); 


        $exposurenet_child_values_value141 = $dom->createElement('Value');

        $exposurenet_child_values_value141_attr_name = new \DOMAttr('Name','Health Care');

        if($data->portfolio_healthcare_status == "1" && $data->portfolio_esc_healthcare_status == "1")
        {
        $exposurenet_child_values_value141_attr_ischecked = new \DOMAttr('IsChecked','true');

        $health_min_val1 = $data->portfolio_esc_healthcare_min/100*$data->portfolio_fixedincome_min/100;

        $health_min_val2 = $data->portfolio_healthcare_min/100*$data->portfolio_equities_min/100;
       
        $health_tot_min_val = ($health_min_val1+$health_min_val2)*100;
       
        if($health_tot_min_val > 100)
        {
        $health_min = round($health_tot_min_val, -2);
        }
        else
        {
        $health_min = round($health_tot_min_val);
        }

        $health_max_val1 = $data->portfolio_esc_healthcare_max/100*$data->portfolio_fixedincome_max/100;

        $health_max_val2 = $data->portfolio_healthcare_max/100*$data->portfolio_equities_max/100;
       
        $health_tot_max_val = ($health_max_val1+$health_max_val2)*100;
       
        if($health_tot_max_val > 100)
        {
        $health_max = round($health_tot_max_val, -2);
        }
        else
        {
        $health_max = round($health_tot_max_val);
        }
        $exposurenet_child_values_value141_attr_min = new \DOMAttr('Min',$health_min/100);

        $exposurenet_child_values_value141_attr_max = new \DOMAttr('Max',$health_max/100);      
      
        }
        else
        {
        $exposurenet_child_values_value141_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value141_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value141_attr_max = new \DOMAttr('Max',1);      
        }
        
        $exposurenet_child_values_value141->setAttributeNode($exposurenet_child_values_value141_attr_name);

        $exposurenet_child_values_value141->setAttributeNode($exposurenet_child_values_value141_attr_ischecked);

        $exposurenet_child_values_value141->setAttributeNode($exposurenet_child_values_value141_attr_min);

        $exposurenet_child_values_value141->setAttributeNode($exposurenet_child_values_value141_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value141); 



        $exposurenet_child_values_value142 = $dom->createElement('Value');

        $exposurenet_child_values_value142_attr_name = new \DOMAttr('Name','Financials');

        if($data->portfolio_financials_status == "1" && $data->portfolio_esc_financials_status == "1")
        {

        $exposurenet_child_values_value142_attr_ischecked = new \DOMAttr('IsChecked','true');

        $financials_min_val1 = $data->portfolio_esc_financials_min/100*$data->portfolio_fixedincome_min/100;

        $financials_min_val2 = $data->portfolio_financials_min/100*$data->portfolio_equities_min/100;
       
        $financials_tot_min_val = ($financials_min_val1+$financials_min_val2)*100;
       
        if($financials_tot_min_val > 100)
        {
        $financials_min = round($financials_tot_min_val, -2);
        }
        else
        {
        $financials_min = round($financials_tot_min_val);
        }

        $financials_max_val1 = $data->portfolio_esc_financials_max/100*$data->portfolio_fixedincome_max/100;

        $financials_max_val2 = $data->portfolio_financials_max/100*$data->portfolio_equities_max/100;
       
        $financials_tot_max_val = ($financials_max_val1+$financials_max_val2)*100;
       
        if($financials_tot_max_val > 100)
        {
        $financials_max = round($financials_tot_max_val, -2);
        }
        else
        {
        $financials_max = round($financials_tot_max_val);
        }
      
        $exposurenet_child_values_value142_attr_min = new \DOMAttr('Min',$financials_min/100);

        $exposurenet_child_values_value142_attr_max = new \DOMAttr('Max',$financials_max/100);
     
        }
        else
        {
        $exposurenet_child_values_value142_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value142_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value142_attr_max = new \DOMAttr('Max',1);
     
        }

        $exposurenet_child_values_value142->setAttributeNode($exposurenet_child_values_value142_attr_name);

        $exposurenet_child_values_value142->setAttributeNode($exposurenet_child_values_value142_attr_ischecked);

        $exposurenet_child_values_value142->setAttributeNode($exposurenet_child_values_value142_attr_min);

        $exposurenet_child_values_value142->setAttributeNode($exposurenet_child_values_value142_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value142); 



        $exposurenet_child_values_value143 = $dom->createElement('Value');

        $exposurenet_child_values_value143_attr_name = new \DOMAttr('Name','Consumer Discretionary');

        if($data->portfolio_discretionary_status == "1" && $data->portfolio_esc_discretionary_status == "1")
        {
        $exposurenet_child_values_value143_attr_ischecked = new \DOMAttr('IsChecked','true');

        $discretionary_min_val1 = $data->portfolio_esc_discretionary_min/100*$data->portfolio_fixedincome_min/100;

        $discretionary_min_val2 = $data->portfolio_discretionary_min/100*$data->portfolio_equities_min/100;
       
        $discretionary_tot_min_val = ($discretionary_min_val1+$discretionary_min_val2)*100;
       
        if($discretionary_tot_min_val > 100)
        {
        $discretionary_min = round($discretionary_tot_min_val, -2);
        }
        else
        {
        $discretionary_min = round($discretionary_tot_min_val);
        }

        $discretionary_max_val1 = $data->portfolio_esc_discretionary_max/100*$data->portfolio_fixedincome_max/100;

        $discretionary_max_val2 = $data->portfolio_discretionary_max/100*$data->portfolio_equities_max/100;
       
        $discretionary_tot_max_val = ($discretionary_max_val1+$discretionary_max_val2)*100;
       
        if($discretionary_tot_max_val > 100)
        {
        $discretionary_max = round($discretionary_tot_max_val, -2);
        }
        else
        {
        $discretionary_max = round($discretionary_tot_max_val);
        }
        $exposurenet_child_values_value143_attr_min = new \DOMAttr('Min',$discretionary_min/100);

        $exposurenet_child_values_value143_attr_max = new \DOMAttr('Max',$discretionary_max/100);

        }
        else
        {
        $exposurenet_child_values_value143_attr_ischecked = new \DOMAttr('IsChecked','false');
    
        $exposurenet_child_values_value143_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value143_attr_max = new \DOMAttr('Max',1);

        }

        $exposurenet_child_values_value143->setAttributeNode($exposurenet_child_values_value143_attr_name);

        $exposurenet_child_values_value143->setAttributeNode($exposurenet_child_values_value143_attr_ischecked);

        $exposurenet_child_values_value143->setAttributeNode($exposurenet_child_values_value143_attr_min);

        $exposurenet_child_values_value143->setAttributeNode($exposurenet_child_values_value143_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value143); 



        $exposurenet_child_values_value144 = $dom->createElement('Value');

        $exposurenet_child_values_value144_attr_name = new \DOMAttr('Name','Communications');

        if($data->portfolio_comservice_status == "1" && $data->portfolio_esc_comservice_status == "1")
        {
        $exposurenet_child_values_value144_attr_ischecked = new \DOMAttr('IsChecked','true');

        $comservice_min_val1 = $data->portfolio_esc_comservice_min/100*$data->portfolio_fixedincome_min/100;

        $comservice_min_val2 = $data->portfolio_comservice_min/100*$data->portfolio_equities_min/100;
       
        $comservice_tot_min_val = ($comservice_min_val1+$comservice_min_val2)*100;
       
        if($comservice_tot_min_val > 100)
        {
        $comservice_min = round($comservice_tot_min_val, -2);
        }
        else
        {
        $comservice_min = round($comservice_tot_min_val);
        }

        $comservice_max_val1 = $data->portfolio_esc_comservice_max/100*$data->portfolio_fixedincome_max/100;

        $comservice_max_val2 = $data->portfolio_comservice_max/100*$data->portfolio_equities_max/100;
       
        $comservice_tot_max_val = ($comservice_max_val1+$comservice_max_val2)*100;
       
        if($comservice_tot_max_val > 100)
        {
        $comservice_max = round($comservice_tot_max_val, -2);
        }
        else
        {
        $comservice_max = round($comservice_tot_max_val);
        }
   

        $exposurenet_child_values_value144_attr_min = new \DOMAttr('Min',$comservice_min/100);

        $exposurenet_child_values_value144_attr_max = new \DOMAttr('Max',$comservice_max/100);      
      
        }
        else
        {
        $exposurenet_child_values_value144_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value144_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value144_attr_max = new \DOMAttr('Max',1);      
      
        }

        $exposurenet_child_values_value144->setAttributeNode($exposurenet_child_values_value144_attr_name);

        $exposurenet_child_values_value144->setAttributeNode($exposurenet_child_values_value144_attr_ischecked);

        $exposurenet_child_values_value144->setAttributeNode($exposurenet_child_values_value144_attr_min);

        $exposurenet_child_values_value144->setAttributeNode($exposurenet_child_values_value144_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value144); 


        $exposurenet_child_values_value145 = $dom->createElement('Value');

        $exposurenet_child_values_value145_attr_name = new \DOMAttr('Name','Industrials');

        if($data->portfolio_esc_industrials_status == "1" && $data->portfolio_industrials_status == "1")
        {
        $exposurenet_child_values_value145_attr_ischecked = new \DOMAttr('IsChecked','true');

        $industrials_min_val1 = $data->portfolio_esc_industrials_min/100*$data->portfolio_fixedincome_min/100;

        $industrials_min_val2 = $data->portfolio_industrials_min/100*$data->portfolio_equities_min/100;
       
        $industrials_tot_min_val = ($industrials_min_val1+$industrials_min_val2)*100;
       
        if($industrials_tot_min_val > 100)
        {
        $industrials_min = round($industrials_tot_min_val, -2);
        }
        else
        {
        $industrials_min = round($industrials_tot_min_val);
        }

        $industrials_max_val1 = $data->portfolio_esc_industrials_max/100*$data->portfolio_fixedincome_max/100;

        $industrials_max_val2 = $data->portfolio_industrials_max/100*$data->portfolio_equities_max/100;
       
        $industrials_tot_max_val = ($industrials_max_val1+$industrials_max_val2)*100;
       
        if($industrials_tot_max_val > 100)
        {
        $industrials_max = round($industrials_tot_max_val, -2);
        }
        else
        {
        $industrials_max = round($industrials_tot_max_val);
        }
              

        $exposurenet_child_values_value145_attr_min = new \DOMAttr('Min',$industrials_min/100);

        $exposurenet_child_values_value145_attr_max = new \DOMAttr('Max',$industrials_max/100);      
    
        }
        else
        {
        $exposurenet_child_values_value145_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value145_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value145_attr_max = new \DOMAttr('Max',1);      

        }
       
        $exposurenet_child_values_value145->setAttributeNode($exposurenet_child_values_value145_attr_name);

        $exposurenet_child_values_value145->setAttributeNode($exposurenet_child_values_value145_attr_ischecked);

        $exposurenet_child_values_value145->setAttributeNode($exposurenet_child_values_value145_attr_min);

        $exposurenet_child_values_value145->setAttributeNode($exposurenet_child_values_value145_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value145); 


        $exposurenet_child_values_value146 = $dom->createElement('Value');

        $exposurenet_child_values_value146_attr_name = new \DOMAttr('Name','Consumer Staples');

        if($data->portfolio_staples_status == "1"  && $data->portfolio_esc_staples_status == "1")
        {

        $exposurenet_child_values_value146_attr_ischecked = new \DOMAttr('IsChecked','true');

        $staples_min_val1 = $data->portfolio_esc_staples_min/100*$data->portfolio_fixedincome_min/100;

        $staples_min_val2 = $data->portfolio_staples_min/100*$data->portfolio_equities_min/100;
       
        $staples_tot_min_val = ($staples_min_val1+$staples_min_val2)*100;
       
        if($staples_tot_min_val > 100)
        {
        $staples_min = round($staples_tot_min_val, -2);
        }
        else
        {
        $staples_min = round($staples_tot_min_val);
        }

        $staples_max_val1 = $data->portfolio_esc_staples_max/100*$data->portfolio_fixedincome_max/100;

        $staples_max_val2 = $data->portfolio_staples_max/100*$data->portfolio_equities_max/100;
       
        $staples_tot_max_val = ($staples_max_val1+$staples_max_val2)*100;
       
        if($staples_tot_max_val > 100)
        {
        $staples_max = round($staples_tot_max_val, -2);
        }
        else
        {
        $staples_max = round($staples_tot_max_val);
        }
              

        $exposurenet_child_values_value146_attr_min = new \DOMAttr('Min',$staples_min/100);

        $exposurenet_child_values_value146_attr_max = new \DOMAttr('Max',$staples_max/100);

        }
        else
        {
        $exposurenet_child_values_value146_attr_ischecked = new \DOMAttr('IsChecked','false');
   
        $exposurenet_child_values_value146_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value146_attr_max = new \DOMAttr('Max',1);
        }

        $exposurenet_child_values_value146->setAttributeNode($exposurenet_child_values_value146_attr_name);

        $exposurenet_child_values_value146->setAttributeNode($exposurenet_child_values_value146_attr_ischecked);

        $exposurenet_child_values_value146->setAttributeNode($exposurenet_child_values_value146_attr_min);

        $exposurenet_child_values_value146->setAttributeNode($exposurenet_child_values_value146_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value146); 


        $exposurenet_child_values_value147 = $dom->createElement('Value');

        $exposurenet_child_values_value147_attr_name = new \DOMAttr('Name','Energy');

        if($data->portfolio_energy_status == "1" && $data->portfolio_esc_energy_status == "1")
        {

        $exposurenet_child_values_value147_attr_ischecked = new \DOMAttr('IsChecked','true');

        $ener_min_val1 = $data->portfolio_esc_energy_min/100*$data->portfolio_fixedincome_min/100;

        $ener_min_val2 = $data->portfolio_energy_min/100*$data->portfolio_equities_min/100;
       
        $ener_tot_min_val = ($ener_min_val1+$ener_min_val2)*100;
       
        if($ener_tot_min_val > 100)
        {
        $ener_min = round($ener_tot_min_val, -2);
        }
        else
        {
        $ener_min = round($ener_tot_min_val);
        }

        $ener_max_val1 = $data->portfolio_esc_energy_max/100*$data->portfolio_fixedincome_max/100;

        $ener_max_val2 = $data->portfolio_energy_max/100*$data->portfolio_equities_max/100;
       
        $ener_tot_max_val = ($ener_max_val1+$ener_max_val2)*100;
       
        if($ener_tot_max_val > 100)
        {
        $ener_max = round($ener_tot_max_val, -2);
        }
        else
        {
        $ener_max = round($ener_tot_max_val);
        }
       
        $exposurenet_child_values_value147_attr_min = new \DOMAttr('Min',$ener_min/100);

        $exposurenet_child_values_value147_attr_max = new \DOMAttr('Max',$ener_max/100);

        }
        else
        {
        $exposurenet_child_values_value147_attr_ischecked = new \DOMAttr('IsChecked','false');
  
        $exposurenet_child_values_value147_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value147_attr_max = new \DOMAttr('Max',1);
        }

        $exposurenet_child_values_value147->setAttributeNode($exposurenet_child_values_value147_attr_name);

        $exposurenet_child_values_value147->setAttributeNode($exposurenet_child_values_value147_attr_ischecked);

        $exposurenet_child_values_value147->setAttributeNode($exposurenet_child_values_value147_attr_min);

        $exposurenet_child_values_value147->setAttributeNode($exposurenet_child_values_value147_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value147); 


        $exposurenet_child_values_value148 = $dom->createElement('Value');

        $exposurenet_child_values_value148_attr_name = new \DOMAttr('Name','Utilities');

        if($data->portfolio_utilities_status == "1" && $data->portfolio_esc_utilities_status == "1")
        {
        $exposurenet_child_values_value148_attr_ischecked = new \DOMAttr('IsChecked','true');

        $utilities_min_val1 = $data->portfolio_esc_utilities_min/100*$data->portfolio_fixedincome_min/100;

        $utilities_min_val2 = $data->portfolio_utilities_min/100*$data->portfolio_equities_min/100;
       
        $utilities_tot_min_val = ($utilities_min_val1+$utilities_min_val2)*100;
       
        if($utilities_tot_min_val > 100)
        {
        $utilities_min = round($utilities_tot_min_val, -2);
        }
        else
        {
        $utilities_min = round($utilities_tot_min_val);
        }

        $utilities_max_val1 = $data->portfolio_esc_utilities_max/100*$data->portfolio_fixedincome_max/100;

        $utilities_max_val2 = $data->portfolio_utilities_max/100*$data->portfolio_equities_max/100;
       
        $utilities_tot_max_val = ($utilities_max_val1+$utilities_max_val2)*100;
       
        if($utilities_tot_max_val > 100)
        {
        $utilities_max = round($utilities_tot_max_val, -2);
        }
        else
        {
        $utilities_max = round($utilities_tot_max_val);
        }

        $exposurenet_child_values_value148_attr_min = new \DOMAttr('Min',$utilities_min/100);

        $exposurenet_child_values_value148_attr_max = new \DOMAttr('Max',$utilities_max/100);
      
        }
        else
        {
        $exposurenet_child_values_value148_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value148_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value148_attr_max = new \DOMAttr('Max',1);

        }
       
        $exposurenet_child_values_value148->setAttributeNode($exposurenet_child_values_value148_attr_name);

        $exposurenet_child_values_value148->setAttributeNode($exposurenet_child_values_value148_attr_ischecked);

        $exposurenet_child_values_value148->setAttributeNode($exposurenet_child_values_value148_attr_min);

        $exposurenet_child_values_value148->setAttributeNode($exposurenet_child_values_value148_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value148); 


        $exposurenet_child_values_value149 = $dom->createElement('Value');

        $exposurenet_child_values_value149_attr_name = new \DOMAttr('Name','Materials');

        if($data->portfolio_esc_materials_status == "1" && $data->portfolio_materials_status == "1")
        {

        $exposurenet_child_values_value149_attr_ischecked = new \DOMAttr('IsChecked','true');

        $materials_min_val1 = $data->portfolio_esc_materials_min/100*$data->portfolio_fixedincome_min/100;

        $materials_min_val2 = $data->portfolio_materials_min/100*$data->portfolio_equities_min/100;
       
        $materials_tot_min_val = ($materials_min_val1+$materials_min_val2)*100;
       
        if($materials_tot_min_val > 100)
        {
        $materials_min = round($materials_tot_min_val, -2);
        }
        else
        {
        $materials_min = round($materials_tot_min_val);
        }

        $materials_max_val1 = $data->portfolio_esc_materials_max/100*$data->portfolio_fixedincome_max/100;

        $materials_max_val2 = $data->portfolio_materials_max/100*$data->portfolio_equities_max/100;
       
        $materials_tot_max_val = ($materials_max_val1+$materials_max_val2)*100;
       
        if($materials_tot_max_val > 100)
        {
        $materials_max = round($materials_tot_max_val, -2);
        }
        else
        {
        $materials_max = round($materials_tot_max_val);
        }

        $exposurenet_child_values_value149_attr_min = new \DOMAttr('Min',$materials_min/100);

        $exposurenet_child_values_value149_attr_max = new \DOMAttr('Max',$materials_max/100);      

        }
        else
        {
        $exposurenet_child_values_value149_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value149_attr_min = new \DOMAttr('Min',0);

        $exposurenet_child_values_value149_attr_max = new \DOMAttr('Max',1);    

        }

        $exposurenet_child_values_value149->setAttributeNode($exposurenet_child_values_value149_attr_name);

        $exposurenet_child_values_value149->setAttributeNode($exposurenet_child_values_value149_attr_ischecked);

        $exposurenet_child_values_value149->setAttributeNode($exposurenet_child_values_value149_attr_min);

        $exposurenet_child_values_value149->setAttributeNode($exposurenet_child_values_value149_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value149); 


        $exposurenet_child_values_value150 = $dom->createElement('Value');

        $exposurenet_child_values_value150_attr_name = new \DOMAttr('Name','Diversified');

        $exposurenet_child_values_value150_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value150_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value150_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value150->setAttributeNode($exposurenet_child_values_value150_attr_name);

        $exposurenet_child_values_value150->setAttributeNode($exposurenet_child_values_value150_attr_ischecked);

        $exposurenet_child_values_value150->setAttributeNode($exposurenet_child_values_value150_attr_min);

        $exposurenet_child_values_value150->setAttributeNode($exposurenet_child_values_value150_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value150); 


        $exposurenet_child_values_value151 = $dom->createElement('Value');

        $exposurenet_child_values_value151_attr_name = new \DOMAttr('Name','Government');

        $exposurenet_child_values_value151_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value151_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value151_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value151->setAttributeNode($exposurenet_child_values_value151_attr_name);

        $exposurenet_child_values_value151->setAttributeNode($exposurenet_child_values_value151_attr_ischecked);

        $exposurenet_child_values_value151->setAttributeNode($exposurenet_child_values_value151_attr_min);

        $exposurenet_child_values_value151->setAttributeNode($exposurenet_child_values_value151_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value151); 


        $exposurenet_child_values_value152 = $dom->createElement('Value');

        $exposurenet_child_values_value152_attr_name = new \DOMAttr('Name','Exposure to CoCos');

        $exposurenet_child_values_value152_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value152_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value152_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value152->setAttributeNode($exposurenet_child_values_value152_attr_name);

        $exposurenet_child_values_value152->setAttributeNode($exposurenet_child_values_value152_attr_ischecked);

        $exposurenet_child_values_value152->setAttributeNode($exposurenet_child_values_value152_attr_min);

        $exposurenet_child_values_value152->setAttributeNode($exposurenet_child_values_value152_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value152); 


        $exposurenet_child_values_value153 = $dom->createElement('Value');

        $exposurenet_child_values_value153_attr_name = new \DOMAttr('Name','Yes');

        $exposurenet_child_values_value153_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value153_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value153_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value153->setAttributeNode($exposurenet_child_values_value153_attr_name);

        $exposurenet_child_values_value153->setAttributeNode($exposurenet_child_values_value153_attr_ischecked);

        $exposurenet_child_values_value153->setAttributeNode($exposurenet_child_values_value153_attr_min);

        $exposurenet_child_values_value153->setAttributeNode($exposurenet_child_values_value153_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value153); 


        $exposurenet_child_values_value154 = $dom->createElement('Value');

        $exposurenet_child_values_value154_attr_name = new \DOMAttr('Name','Exposure to Structured Credit');

        $exposurenet_child_values_value154_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value154_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value154_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value154->setAttributeNode($exposurenet_child_values_value154_attr_name);

        $exposurenet_child_values_value154->setAttributeNode($exposurenet_child_values_value154_attr_ischecked);

        $exposurenet_child_values_value154->setAttributeNode($exposurenet_child_values_value154_attr_min);

        $exposurenet_child_values_value154->setAttributeNode($exposurenet_child_values_value154_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value154); 


        $exposurenet_child_values_value155 = $dom->createElement('Value');

        $exposurenet_child_values_value155_attr_name = new \DOMAttr('Name','Yes');

        $exposurenet_child_values_value155_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value155_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value155_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value155->setAttributeNode($exposurenet_child_values_value155_attr_name);

        $exposurenet_child_values_value155->setAttributeNode($exposurenet_child_values_value155_attr_ischecked);

        $exposurenet_child_values_value155->setAttributeNode($exposurenet_child_values_value155_attr_min);

        $exposurenet_child_values_value155->setAttributeNode($exposurenet_child_values_value155_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value155); 

        $exposurenet_child_values_value156 = $dom->createElement('Value');

        $exposurenet_child_values_value156_attr_name = new \DOMAttr('Name','Shariah Compliance');
 
        if($data->portfolio_shariahcomplaint == "1")
        {
        $exposurenet_child_values_value156_attr_ischecked = new \DOMAttr('IsChecked','true');

        $exposurenet_child_values_value156_attr_min = new \DOMAttr('Min','1');

        $exposurenet_child_values_value156_attr_max = new \DOMAttr('Max','1');
        }
        elseif($data->portfolio_shariahcomplaint == "0")
        {
        $exposurenet_child_values_value156_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value156_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value156_attr_max = new \DOMAttr('Max','1');
        }
        $exposurenet_child_values_value156->setAttributeNode($exposurenet_child_values_value156_attr_name);

        $exposurenet_child_values_value156->setAttributeNode($exposurenet_child_values_value156_attr_ischecked);

        $exposurenet_child_values_value156->setAttributeNode($exposurenet_child_values_value156_attr_min);

        $exposurenet_child_values_value156->setAttributeNode($exposurenet_child_values_value156_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value156); 


        $exposurenet_child_values_value157 = $dom->createElement('Value');

        $exposurenet_child_values_value157_attr_name = new \DOMAttr('Name','Yes');

        $exposurenet_child_values_value157_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value157_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value157_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value157->setAttributeNode($exposurenet_child_values_value157_attr_name);

        $exposurenet_child_values_value157->setAttributeNode($exposurenet_child_values_value157_attr_ischecked);

        $exposurenet_child_values_value157->setAttributeNode($exposurenet_child_values_value157_attr_min);

        $exposurenet_child_values_value157->setAttributeNode($exposurenet_child_values_value157_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value157); 

      

        $exposurenet_child_values_value159 = $dom->createElement('Value');

        $exposurenet_child_values_value159_attr_name = new \DOMAttr('Name','Cash Flow Yield');

        $exposurenet_child_values_value159_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value159_attr_min = new \DOMAttr('Min',$data->portfolio_cashflow_yield/100);

        $exposurenet_child_values_value159_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value159->setAttributeNode($exposurenet_child_values_value159_attr_name);

        $exposurenet_child_values_value159->setAttributeNode($exposurenet_child_values_value159_attr_ischecked);

        $exposurenet_child_values_value159->setAttributeNode($exposurenet_child_values_value159_attr_min);

        $exposurenet_child_values_value159->setAttributeNode($exposurenet_child_values_value159_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value159); 


        $exposurenet_child_values_value160 = $dom->createElement('Value');

        $exposurenet_child_values_value160_attr_name = new \DOMAttr('Name','Exposure to Vice Industries');

        $exposurenet_child_values_value160_attr_ischecked = new \DOMAttr('IsChecked','true');

        $vice_ind_min_val1 = $data->portfolio_esc_viceindustries_min/100*$data->portfolio_fixedincome_min/100;

        $vice_ind_min_val2 = $data->portfolio_viceindustries_min/100*$data->portfolio_equities_min/100;
       
        $vice_ind_tot_min_val = ($vice_ind_min_val1+$vice_ind_min_val2)*100;
       
        if($vice_ind_tot_min_val > 100)
        {
        $vice_ind_min = round($vice_ind_tot_min_val, -2);
        }
        else
        {
        $vice_ind_min = round($vice_ind_tot_min_val);
        }

        $vice_ind_max_val1 = $data->portfolio_esc_viceindustries_max/100*$data->portfolio_fixedincome_max/100;

        $vice_ind_max_val2 = $data->portfolio_viceindustries_max/100*$data->portfolio_equities_max/100;
       
        $vice_ind_tot_max_val = ($vice_ind_max_val1+$vice_ind_max_val2)*100;
       
        if($vice_ind_tot_max_val > 100)
        {
        $vice_ind_max = round($vice_ind_tot_max_val, -2);
        }
        else
        {
        $vice_ind_max = round($vice_ind_tot_max_val);
        }

        $exposurenet_child_values_value160_attr_min = new \DOMAttr('Min',$vice_ind_min/100);

        $exposurenet_child_values_value160_attr_max = new \DOMAttr('Max',$vice_ind_max/100);
      
        $exposurenet_child_values_value160->setAttributeNode($exposurenet_child_values_value160_attr_name);

        $exposurenet_child_values_value160->setAttributeNode($exposurenet_child_values_value160_attr_ischecked);

        $exposurenet_child_values_value160->setAttributeNode($exposurenet_child_values_value160_attr_min);

        $exposurenet_child_values_value160->setAttributeNode($exposurenet_child_values_value160_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value160); 



        $exposurenet_child_values_value161 = $dom->createElement('Value');

        $exposurenet_child_values_value161_attr_name = new \DOMAttr('Name','Yes');

        $exposurenet_child_values_value161_attr_ischecked = new \DOMAttr('IsChecked','false');

        $exposurenet_child_values_value161_attr_min = new \DOMAttr('Min','0');

        $exposurenet_child_values_value161_attr_max = new \DOMAttr('Max','1');

        $exposurenet_child_values_value161->setAttributeNode($exposurenet_child_values_value161_attr_name);

        $exposurenet_child_values_value161->setAttributeNode($exposurenet_child_values_value161_attr_ischecked);

        $exposurenet_child_values_value161->setAttributeNode($exposurenet_child_values_value161_attr_min);

        $exposurenet_child_values_value161->setAttributeNode($exposurenet_child_values_value161_attr_max); 

        $exposurenet_child_values->appendChild($exposurenet_child_values_value161); 

        $exposurenet_node->appendChild($exposurenet_child_values);

        $constraints_node->appendChild($exposurenet_node);

        $portfolio_node->appendChild($constraints_node);

        $portfolios_node->appendChild($portfolio_node);

        $body_node->appendChild($portfolios_node);

        $root->appendChild($head_node);

        $root->appendChild($body_node);

        $dom->appendChild($root);

        $data = $dom->saveXML();

    //  dd($dom->saveXML());

        $file = $eventid.'.xml';

       $destinationPath=public_path()."/input/";

      // $destinationPath = "/test/input/";

   if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
     
       File::put($destinationPath.$file,$data);

  // \Storage::disk('custom_folder_1')->put($destinationPath.$file,$data);

        return response()->download($destinationPath.$file);
    }
}
