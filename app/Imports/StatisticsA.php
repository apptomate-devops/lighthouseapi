<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class StatisticsA implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
            if ( $row[0] != '') {
            if($row[225]){
                $row[225]=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[225])->format('d-m-Y');
            }else{
                $row[225]="";
            }
            if($row[226]){
                $row[226]=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[226])->format('d-m-Y');
            }else{
                $row[226]="";
            }
             $data=DB::table('lc_statistics_a')->insert([
    ['sta_a_client_id' => $portfolio_user,'sta_a_portfolio_id' => $portfolio_id,'sta_a_assets'=>$row[0],'sta_a_weight'=>$row[1],'sta_a_historical_annual_assets_inception'=>$row[2],'sta_a_historical_annual_time_window'=>$row[3],'sta_a_equilibrium_annual_mean'=>$row[4],'sta_a_annual_volatility_assets_inception'=>$row[5],'sta_a_annual_volatility_time_window'=>$row[6],'sta_a_skewness'=>$row[7],'sta_a_excess_kurtosis'=>$row[8],'sta_a_last_1_month_return'=>$row[9],'sta_a_last_3_months_return'=>$row[10],'sta_a_last_12_months_return'=>$row[11],'sta_a_year-to-date_return'=>$row[12],'sta_a_annual_last_3_months_return'=>$row[13],'sta_a_annual_last_3_years_return'=>$row[14],'sta_a_annual_last_5_years_return'=>$row[15],'sta_a_annual_volatility_last_12_months'=>$row[16],'sta_a_annual_volatility_last_3_years'=>$row[17],'sta_a_annual_volatility_last_5_years'=>$row[18],'sta_a_normal_monthly_var_99'=>$row[19],'sta_a_modified_monthly_var_99'=>$row[20],'sta_a_conditional_monthly_var_99'=>$row[21],'sta_a_normal_monthly_var_97'=>$row[22],'sta_a_modified_monthly_var_97'=>$row[23],'sta_a_conditional_monthly_var_97'=>$row[24],'sta_a_normal_monthly_var_95'=>$row[25],'sta_a_modified_monthly_var_95'=>$row[26],'sta_a_conditional_monthly_var_95'=>$row[27],'sta_a_correlation_MSCI_ACWI'=>$row[28],'sta_a_correlation_global_equities_fiftyig'=>$row[29],'sta_a_bull_correlation_MSCI_ACWI'=>$row[30],'sta_a_bear_correlation_MSCI_ACWI'=>$row[31],'sta_a_bull_correlation_global_equities_fiftyig'=>$row[32],'sta_a_bear_correlation_global_equities_fiftyig'=>$row[33],'sta_a_beta_MSCI_ACWI'=>$row[34],'sta_a_beta_MSCI_ACWI_three_years'=>$row[35],'sta_a_beta_MSCI_ACWI_five_years'=>$row[36],'sta_a_beta_global_equities_fiftyig'=>$row[37],'sta_a_beta_global_equities_fiftyig_three_years'=>$row[38],'sta_a_beta_global_equities_fiftyig_five_years'=>$row[39],'sta_a_bull_beta_MSCI_ACWI'=>$row[40],'sta_a_bear_beta_MSCI_ACWI'=>$row[41],'sta_a_bull_beta_global_equities_fiftyig'=>$row[42],'sta_a_bear_beta_global_equities_fiftyig'=>$row[43],'sta_a_annual_sharpe_ratio'=>$row[44],'sta_a_annual_sharpe_ratio_three_years'=>$row[45],'sta_a_annual_sharpe_ratio_five_years'=>$row[46],'sta_a_downside_deviation_0'=>$row[47],'sta_a_downside_deviation_rf'=>$row[48],'sta_a_annual_treynor_ratio'=>$row[49],'sta_a_sortino_ratio_0'=>$row[50],'sta_a_sortino_ratio_rf'=>$row[51],'sta_a_sterling_ratio'=>$row[52],'sta_a_calmar_ratio'=>$row[53],'sta_a_hurst_index'=>$row[54],'sta_a_omega_0_00'=>$row[55],'sta_a_omega_0_50'=>$row[56],'sta_a_omega_1_00'=>$row[57],'sta_a_omega_1_50'=>$row[58],'sta_a_omega_2_00'=>$row[59],'sta_a_autocorrelated'=>$row[60],'sta_a_up_months'=>$row[61],'sta_a_down_months'=>$row[62],'sta_a_average_bull_market'=>$row[63],'sta_a_average_bear_market'=>$row[64],'sta_a_average_bull_benchmark2'=>$row[65],'sta_a_average_bear_benchmark2'=>$row[66],'sta_a_maximum_consecutive_gain'=>$row[67],'sta_a_maximum_consecutive_loss'=>$row[68],'sta_a_max_monthly_gain'=>$row[69],'sta_a_max_monthly_loss'=>$row[70],'sta_a_max_drawdown'=>$row[71],'sta_a_date_max_drawdown'=>$row[72],'sta_a_startdate_drawdown'=>$row[73],'sta_a_enddate_drawdown'=>$row[74],'sta_a_max_time_under_the_water'=>$row[75],'sta_a_max_possible_drawdown_99'=>$row[76],'sta_a_max_possible_drawdown_99_9'=>$row[77],'sta_a_max_possible_time_under_the_water_99'=>$row[78],'sta_a_max_possible_time_under_the_water_99_9'=>$row[79],'sta_a_monthly_portfolio_may_20'=>$row[80],'sta_a_strategy'=>$row[81],'sta_a_inception_date'=>$row[82],'sta_a_quantitative_rating'=>$row[83],'sta_a_qualitative_rating'=>$row[84],'sta_a_final_rating'=>$row[85],'sta_a_last_return_date'=>$row[86],'sta_a_company_name'=>$row[87],'sta_a_manager_name'=>$row[88],'sta_a_address'=>$row[89],'sta_a_phone_number'=>$row[90],'sta_a_email'=>$row[91],'sta_a_asset_type'=>$row[92],'sta_a_proprietary_id'=>$row[93],'sta_a_country'=>$row[94],'sta_a_currency'=>$row[95],'sta_a_sub_strategy'=>$row[96],'sta_a_size_m'=>$row[97],'sta_a_status'=>$row[98],'sta_a_redemption_period'=>$row[99],'sta_a_redemption_period2'=>$row[100],'sta_a_redemption_period3'=>$row[101],'sta_a_notice_period'=>$row[102],'sta_a_performance_fee'=>$row[103],'sta_a_management_fee'=>$row[104],'sta_a_isin'=>$row[105],'sta_a_return_2015'=>$row[106],'sta_a_return_2016'=>$row[107],'sta_a_return_2017'=>$row[108],'sta_a_return_2018'=>$row[109],'sta_a_return_2019'=>$row[110],'sta_a_prime_broker'=>$row[111],'sta_a_return'=>$row[112],'sta_a_information_ratio_MSCI_ACWI'=>$row[113],'sta_a_information_ratio_global_equities_fiftyig'=>$row[114],'sta_a_tracking_error_MSCI_ACWI'=>$row[115],'sta_a_tracking_error_global_equities_fiftyig'=>$row[116],'sta_a_R2_MSCI_ACWI'=>$row[117],'sta_a_R2_global_equities_fiftyig'=>$row[118],'sta_a_on_offshore'=>$row[119],'sta_a_quater_to_date'=>$row[120],'sta_a_annual_sinceinception'=>$row[121],'sta_a_annual_bear_market_MSCI_ACWI'=>$row[122],'sta_a_annual_bull_market_MSCI_ACWI'=>$row[123],'sta_a_annual_bear_market_global_equities_fiftyig'=>$row[124],'sta_a_annual_bull_market_global_equities_fiftyig'=>$row[125],'sta_a_average_positive_return'=>$row[126],'sta_a_average_negative_return'=>$row[127],'sta_a_outperformance_ MSCI_ACWI_index_last_month'=>$row[128],'sta_a_outperformance_global_equities_fiftyig_last_month'=>$row[129],'sta_a_annual_outperformance_MSCI_ACWI'=>$row[130],'sta_a_annual_outperformance_global_equities_fiftyig'=>$row[131],'sta_a_rachev_ratio_99'=>$row[132],'sta_a_rachev_ratio_97'=>$row[133],'sta_a_rachev_ratio_95'=>$row[134],'sta_a_rachev_ratio_90'=>$row[135],'sta_a_annual_up_capture_MSCI_ACWI'=>$row[136],'sta_a_annual_up_capture_global_equities_fiftyig'=>$row[137],'sta_a_annual_down_capture_MSCI_ACWI'=>$row[138],'sta_a_annual_down_capture_global_equities_fiftyig'=>$row[139],'sta_a_annual_tracking_error_MSCI_ACWI'=>$row[140],'sta_a_annual_tracking_error_global_equities_fiftyig'=>$row[141],'sta_a_annual_jensen_alpha_MSCI_ACWI'=>$row[142],'sta_a_annual_jensen_alpha_global_equities_fiftyig'=>$row[143],'sta_a_annual_information_ratio_MSCI_ACWI'=>$row[144],'sta_a_annual_information_ratio_global_equities_fiftyig'=>$row[145],'sta_a_alpha_MSCI_ACWI'=>$row[146],'sta_a_alpha_global_equities_fiftyig'=>$row[147],'sta_a_alpha_global_equities_fiftyig'=>$row[148],'sta_a_exponential_annual_volatility'=>$row[149],'sta_a_exponential_annual_volatility_12months'=>$row[150],'sta_a_exponential_annual_volatility_3years'=>$row[151],'sta_a_exponential_correlation_MSCI_ACWI'=>$row[152],'sta_a_exponential_correlation_global_equities_fiftyig'=>$row[153],'sta_a_exponential_annual_sharpe_ratio'=>$row[154],'sta_a_exponential_normal_monthly_var_99'=>$row[155],'sta_a_exponential_normal_monthly_var_97'=>$row[156],'sta_a_exponential_normal_monthly_var_95'=>$row[157],'sta_a_exponential_normal_monthly_var_90'=>$row[158],'sta_a_exponential_normal_weekly_var_99'=>$row[159],'sta_a_exponential_normal_weekly_var_97'=>$row[160],'sta_a_exponential_normal_weekly_var_95'=>$row[161],'sta_a_exponential_normal_weekly_var_90'=>$row[162],'sta_a_exponential_normal_daily_var_99'=>$row[163],'sta_a_exponential_normal_daily_var_97'=>$row[164],'sta_a_exponential_normal_daily_var_95'=>$row[165],'sta_a_exponential_normal_daily_var_90'=>$row[166],'sta_a_bias_ratio'=>$row[167],'sta_a_manager_biography'=>$row[168],'sta_a_minimum_investment'=>$row[169],'sta_a_auditor'=>$row[170],'sta_a_custodian'=>$row[171],'sta_a_administrator'=>$row[172],'sta_a_proxy_1_start_date'=>$row[173],'sta_a_proxy_1_end_date'=>$row[174],'sta_a_proxy_2_start_date'=>$row[175],'sta_a_proxy_2_end_date'=>$row[176],'sta_a_proxy_3_start_date'=>$row[177],'sta_a_proxy_3_end_date'=>$row[178],'sta_a_fund_domicile'=>$row[179],'sta_a_fund_contact'=>$row[180],'sta_a_fund_contact_email'=>$row[181],'sta_a_fund_contact_number'=>$row[182],'sta_a_city'=>$row[183],'sta_a_leverage'=>$row[184],'sta_a_lockup_1_duration'=>$row[185],'sta_a_lockup_2_duration'=>$row[186],'sta_a_lockup_3_duration'=>$row[187],'sta_a_gate'=>$row[188],'sta_a_gate_frequency'=>$row[189],'sta_a_holdback_calendar_2'=>$row[190],'sta_a_holdback_days_2'=>$row[191],'sta_a_holdback_paidafter_2'=>$row[192],'sta_a_holdback_calendar_3'=>$row[193],'sta_a_holdback_days_3'=>$row[194],'sta_a_holdback_paidafter_3'=>$row[195],'sta_a_holdback_calendar_4'=>$row[196],'sta_a_holdback_days_4'=>$row[197],'sta_a_holdback_paidafter_4'=>$row[198],'sta_a_initial_payment'=>$row[199],'sta_a_lockup_1_fee'=>$row[200],'sta_a_lockup_2_fee'=>$row[201],'sta_a_lockup_3_fee'=>$row[202],'sta_a_lockup_1_type'=>$row[203],'sta_a_lockup_2_type'=>$row[204],'sta_a_lockup_3_type'=>$row[205],'sta_a_redemption_calendar_1'=>$row[206],'sta_a_redemption_calendar_2'=>$row[207],'sta_a_redemption_calendar_3'=>$row[208],'sta_a_redemption_fee_1'=>$row[209],'sta_a_redemption_fee_2'=>$row[210],'sta_a_redemption_fee_3'=>$row[211],'sta_a_redemption_frequency_1'=>$row[212],'sta_a_redemption_frequency_2'=>$row[213],'sta_a_redemption_frequency_3'=>$row[214],'sta_a_subscription_calendar'=>$row[215],'sta_a_second_max_drawdown'=>$row[216],'sta_a_date_second_max_drawdown'=>$row[217],'sta_a_bloomberg_ticker'=>$row[218],'sta_a_bond_rank'=>$row[219],'sta_a_currentprice'=>$row[220],'sta_a_lc_target_price'=>$row[221],'sta_a_coupon'=>$row[222],'sta_a_yield_worst'=>$row[223],'sta_a_effective_duration'=>$row[224],'sta_a_maturity_date'=>$row[225],'sta_a_call_date'=>$row[226],'sta_a_bloomberg_composite_ranking'=>$row[227],'sta_a_investment_geography'=>$row[228],'sta_a_industry_focus'=>$row[229],'sta_a_vice_stock_indicator'=>$row[230],'sta_a_addedon'=>Carbon::now(),'sta_a_addedby'=>1,'sta_a_updatedon'=>Carbon::now(),'sta_a_updatedby'=>1]
   ]);
    }
    else
    {
        return;
    }
    }
 }
     /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}