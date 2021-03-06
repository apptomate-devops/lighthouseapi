<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Session;


class Statistics_pb implements ToCollection,WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
     foreach ($rows as $row) 
       {
        if ( $row[0] != '') {
     $data=DB::table('lc_statistics_pb')->insert([
         [
        'sta_pb_assets'=>$row[0],
        'sta_pb_weight'=>$row[1],
        'sta_pb_expected_annual_return'=>$row[2],
        'sta_pb_expected_annual_volatility'=>$row[3],
        'sta_pb_expected_annual_drawdown'=>$row[4],
        'sta_pb_his_annual_return_sin_asset_inception'=>$row[5],
        'sta_pb_his_annual_return_in_time_window'=>$row[6],
        'sta_pb_equilibrium_annual_mean'=>$row[7],
        'sta_pb_annual_volatility_sin_asset_inception'=>$row[8],
        'sta_pb_annual_volatility_in_time_window'=>$row[9],
        'sta_pb_skewness'=>$row[10],
        'sta_pb_excess_kurtosis'=>$row[11],
        'sta_pb_last_1_month_return'=>$row[12],
        'sta_pb_last_3_month_return'=>$row[13],
        'sta_pb_last_12_month_return'=>$row[14],
        'sta_pb_year_to_date_return'=>$row[15],
        'sta_pb_annual_last_3_month_return'=>$row[16],
        'sta_pb_annual_last_3_year_return'=>$row[17],
        'sta_pb_annual_last_5_year_return'=>$row[18],
        'sta_pb_annual_volatility_last_12_month'=>$row[19],
        'sta_pb_annual_volatility_last_3_year'=>$row[20],
        'sta_pb_annual_volatility_last_5_year'=>$row[21],
        'sta_pb_normal_month_var_99'=>$row[22],
        'sta_pb_modified_month_var_99'=>$row[23],
        'sta_pb_conditional_month_var_99'=>$row[24],
        'sta_pb_normal_month_var_97'=>$row[25],
        'sta_pb_modified_month_var_97'=>$row[26],
        'sta_pb_conditional_month_var_97'=>$row[27],
        'sta_pb_normal_month_var_95'=>$row[28],
        'sta_pb_modified_month_var_95'=>$row[29],
        'sta_pb_conditional_month_var_95'=>$row[30],
        'sta_pb_correlation_msci_index'=>$row[31],
        'sta_pb_correlation_50_global_equities_50_lg'=>$row[32],
        'sta_pb_bull_correlation_msci_index'=>$row[33],
        'sta_pb_bear_correlation_msci_index'=>$row[34],
        'sta_pb_bull_correlation_50_global_equities_50'=>$row[35],
        'sta_pb_bear_correlation_50_global_equities_50'=>$row[36],
        'sta_pb_beta_msci_index'=>$row[37],
        'sta_pb_beta_msci_index_last_3_year'=>$row[38],
        'sta_pb_beta_msci_index_last_5_year'=>$row[39],
        'sta_pb_beta_50_global_equities_50'=>$row[40],
        'sta_pb_beta_50_global_equities_50_last_3_year'=>$row[41],
        'sta_pb_beta_50_global_equities_50_last_5_year'=>$row[42],
        'sta_pb_bull_beta_msci_index'=>$row[43],
        'sta_pb_bear_beta_msci_index'=>$row[44],
        'sta_pb_bull_beta_global_equities_50'=>$row[45],
        'sta_pb_bear_beta_global_equities_50'=>$row[46],
        'sta_pb_annual_sharpe_ratio'=>$row[47],
        'sta_pb_annual_sharpe_ratio_last_3_year'=>$row[48],
        'sta_pb_annual_sharpe_ratio_last_5_year'=>$row[49],
        'sta_pb_downside_deviation_vs_0'=>$row[50],
        'sta_pb_downside_deviation_vs_rf'=>$row[51],
        'sta_pb_annual_treynor_ratio'=>$row[52],
        'sta_pb_sortino_ratio_vs_0'=>$row[53],
        'sta_pb_sortino_ratio_vs_rf'=>$row[54],
        'sta_pb_sterling_ratio'=>$row[55],
        'sta_pb_calmar_ratio'=>$row[56],
        'sta_pb_hurst_index'=>$row[57],
        'sta_pb_omega_0_00'=>$row[58],
        'sta_pb_omega_0_50'=>$row[59],
        'sta_pb_omega_1_00'=>$row[60],
        'sta_pb_omega_1_50'=>$row[61],
        'sta_pb_omega_2_00'=>$row[62],
        'sta_pb_autocorrelated'=>$row[63],
        'sta_pb_upmonth'=>$row[64],
        'sta_pb_downmoth'=>$row[65],
        'sta_pb_avg_return_during_bull_market'=>$row[66],
        'sta_pb_avg_return_during_bear_market'=>$row[67],
        'sta_pb_avg_return_during_bull_benchmark2'=>$row[68],
        'sta_pb_avg_return_during_bear_benchmark2'=>$row[69],
        'sta_pb_maximum_consecutive_gain'=>$row[70],
        'sta_pb_maximum_consecutive_loss'=>$row[71],
        'sta_pb_max_monthly_gain'=>$row[72],
        'sta_pb_max_monthly_loss'=>$row[73],
        'sta_pb_max_drawdown'=>$row[74],
        'sta_pb_dateof_max_drawdown'=>$row[75],
        'sta_pb_start_date_drawdown'=>$row[76],
        'sta_pb_end_date_drawdown'=>$row[77],
        'sta_pb_max_time_under_the_water_month'=>$row[78],
        'sta_pb_max_possible_drawdown_99'=>$row[79],
        'sta_pb_max_possible_drawdown_99_9'=>$row[80],
        'sta_pb_max_time_under_the_water_month_99'=>$row[81],
        'sta_pb_max_time_under_the_water_month_99_9'=>$row[82],
        'sta_pb_monthly_portfolio_ret_attribution_may_20'=>$row[83],
        'sta_pb_strategy'=>$row[84],
        'sta_pb_inception_date'=>$row[85],
        'sta_pb_quantitative_rating'=>$row[86],
        'sta_pb_qualitative_rating'=>$row[87],
        'sta_pb_final_rating'=>$row[88],
        'sta_pb_last_return_date'=>$row[89],
        'sta_pb_company_name'=>$row[90],
        'sta_pb_manager_name'=>$row[91],
        'sta_pb_address'=>$row[92],
        'sta_pb_phone_number'=>$row[93],
        'sta_pb_email'=>$row[94],
        'sta_pb_asset_type'=>$row[95],
        'sta_pb_proprietary_id'=>$row[96],
        'sta_pb_country'=>$row[97],
        'sta_pb_currency'=>$row[98],
        'sta_pb_sub_strategy'=>$row[99],
        'sta_pb_size'=>$row[100],
        'sta_pb_status'=>$row[101],
        'sta_pb_redemption_period'=>$row[102],
        'sta_pb_redemption_period_2'=>$row[103],
        'sta_pb_redemption_period_3'=>$row[104],
        'sta_pb_notice_period'=>$row[105],
        'sta_pb_performance_fee'=>$row[106],
        'sta_pb_management_fee'=>$row[107],
        'sta_pb_isin'=>$row[108],
        'sta_pb_return_2015'=>$row[109],
        'sta_pb_return_2016'=>$row[110],
        'sta_pb_return_2017'=>$row[111],
        'sta_pb_return_2018'=>$row[112],
        'sta_pb_return_2019'=>$row[113],
        'sta_pb_prime_broker'=>$row[114],
        'sta_pb_return'=>$row[115],
        'sta_pb_info_ratio_vs_msci_index'=>$row[116],
        'sta_pb_info_ratio_vs_global_equities'=>$row[117],
        'sta_pb_tracking_error_vs_msci_index'=>$row[118],
        'sta_pb_tracking_error_vs_global_equities'=>$row[119],
        'sta_pb_r2_vs_msci_index'=>$row[120],
        'sta_pb_r2_vs_global_equities'=>$row[121],
        'sta_pb_on_offshore'=>$row[122],
        'sta_pb_quarter_to_date'=>$row[123],
        'sta_pb_annual_return_sinceinception'=>$row[124],
        'sta_pb_annual_bear_market_ret_vs_msci_index'=>$row[125],
        'sta_pb_annual_bull_market_ret_vs_msci_index'=>$row[126],
        'sta_pb_annual_bear_market_ret_vs_global_equities'=>$row[127],
        'sta_pb_annual_bull_market_ret_vs_gloabal_equities'=>$row[128],
        'sta_pb_avg_positive_return'=>$row[129],
        'sta_pb_avg_negative_return'=>$row[130],
        'sta_pb_outperforme_vs_msci_index_last_month'=>$row[131],
        'sta_pb_outperforme_vs_global_equities_last_month'=>$row[132],
        'sta_pb_annual_outperforme_vs_msci_index'=>$row[133],
        'sta_pb_annual_outperforme_vs_global_equities'=>$row[134],
        'sta_pb_rachev_ratio_99'=>$row[135],
        'sta_pb_rachev_ratio_97'=>$row[136],
        'sta_pb_rachev_ratio_95'=>$row[137],
        'sta_pb_rachev_ratio_90'=>$row[138],
        'sta_pb_annual_up_cap_vs_msci_index'=>$row[139],
        'sta_pb_annual_up_cap_vs_gloabl_equities'=>$row[140],
        'sta_pb_annual_down_cap_vs_msci_index'=>$row[141],
        'sta_pb_annual_down_cap_vs_gloabl_equities'=>$row[142],
        'sta_pb_annual_tracking_err_vs_msci_index_beta1'=>$row[143],
        'sta_pb_annual_tracking_err_vs_gloabl_equities_beta1'=>$row[144],
        'sta_pb_annual_jensen_alpha_vs_msci_index_vs_rf'=>$row[145],
        'sta_pb_annual_jensen_alpha_vs_global_equities_vs_rf'=>$row[146],
        'sta_pb_annual_info_ratio_vs_msci_index_beta1'=>$row[147],
        'sta_pb_annual_info_ratio_vs_global_equities_beta1'=>$row[148],
        'sta_pb_alpha_vs_msci_index'=>$row[149],
        'sta_pb_alpha_vs_global_equities'=>$row[150],
        'sta_pb_company_aum_m'=>$row[151],
        'sta_pb_exponential_annual_volatility'=>$row[152],
        'sta_pb_exponential_annual_volatility_last_12_month'=>$row[153],
        'sta_pb_exponential_annual_volatility_last_3_year'=>$row[154],
        'sta_pb_exponential_correlation_msci_index'=>$row[155],
        'sta_pb_exponential_correlation_gloabl_equities'=>$row[156],
        'sta_pb_exponential_annual_sharpe_ratio_vs_rf'=>$row[157],
        'sta_pb_exponential_normal_monthly_var_99'=>$row[158],
        'sta_pb_exponential_normal_monthly_var_97'=>$row[159],
        'sta_pb_exponential_normal_monthly_var_95'=>$row[160],
        'sta_pb_exponential_normal_monthly_var_90'=>$row[161],
        'sta_pb_exponential_normal_weekly_var_99'=>$row[162],
        'sta_pb_exponential_normal_weekly_var_97'=>$row[163],
        'sta_pb_exponential_normal_weekly_var_95'=>$row[164],
        'sta_pb_exponential_normal_weekly_var_90'=>$row[165],
        'sta_pb_exponential_normal_daily_var_99'=>$row[166],
        'sta_pb_exponential_normal_daily_var_97'=>$row[167],
        'sta_pb_exponential_normal_daily_var_95'=>$row[168],
        'sta_pb_exponential_normal_daily_var_90'=>$row[169],
        'sta_pb_bias_ratio'=>$row[170],
        'sta_pb_manger_bio'=>$row[171],
        'sta_pb_min_investment'=>$row[172],
        'sta_pb_auditor'=>$row[173],
        'sta_pb_custodian'=>$row[174],
        'sta_pb_administrator'=>$row[175],
        'sta_pb_proxy1_start_date'=>$row[176],
        'sta_pb_proxy1_end_date'=>$row[177],
        'sta_pb_proxy2_start_date'=>$row[178],
        'sta_pb_proxy2_end_date'=>$row[179],
        'sta_pb_proxy3_start_date'=>$row[180],
        'sta_pb_proxy3_end_date'=>$row[181],
        'sta_pb_fund_domicile'=>$row[182],
        'sta_pb_fund_contact'=>$row[183],
        'sta_pb_fund_contact_email'=>$row[184],
        'sta_pb_fund_contact_number'=>$row[185],
        'sta_pb_city'=>$row[186],
        'sta_pb_leverage'=>$row[187],
        'sta_pb_lockup1_duration'=>$row[188],
        'sta_pb_lockup2_duration'=>$row[189],
        'sta_pb_lockup3_duration'=>$row[190],
        'sta_pb_gate'=>$row[191],
        'sta_pb_gate_frequency'=>$row[192],
        'sta_pb_holdback_calendar2'=>$row[193],
        'sta_pb_holdback_days2'=>$row[194],
        'sta_pb_holdbak_paidafter2'=>$row[195],
        'sta_pb_holdback_calendar3'=>$row[196],
        'sta_pb_holdback_days3'=>$row[197],
        'sta_pb_holdbak_paidafter3'=>$row[198],
        'sta_pb_holdback_calendar4'=>$row[199],
        'sta_pb_holdback_days4'=>$row[200],
        'sta_pb_holdbak_paidafter4'=>$row[201],
        'sta_pb_initial_payment'=>$row[202],
        'sta_pb_lockup1_fee'=>$row[203],
        'sta_pb_lockup2_fee'=>$row[204],
        'sta_pb_lockup3_fee'=>$row[205],
        'sta_pb_lockup1_type'=>$row[206],
        'sta_pb_lockup2_type'=>$row[207],
        'sta_pb_lockup3_type'=>$row[208],
        'sta_pb_redemption_calendar1'=>$row[209],
        'sta_pb_redemption_calendar2'=>$row[210],
        'sta_pb_redemption_calendar3'=>$row[211],
        'sta_pb_redemption_fee1'=>$row[212],
        'sta_pb_redemption_fee2'=>$row[213],
        'sta_pb_redemption_fee3'=>$row[214],
        'sta_pb_redemption_frequency1'=>$row[215],
        'sta_pb_redemption_frequency2'=>$row[216],
        'sta_pb_redemption_frequency3'=>$row[217],
        'sta_pb_subscription_calendar'=>$row[218],
        'sta_pb_second_max_drawdown'=>$row[219],
        'sta_pb_date_second_max_drawdown'=>$row[220],
        'sta_pb_portfolio_id'=>$portfolio_id,
        'sta_pb_client_id'=>$portfolio_user,
        'sta_pb_addedon'=>Carbon::now(),
        'sta_pb_addedby'=>1,
        'sta_pb_uddatedon'=>Carbon::now(),
        'sta_pb_updatedby'=>1]
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
}