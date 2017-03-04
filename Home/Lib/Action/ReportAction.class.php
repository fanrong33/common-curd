<?php
/**
 * 报告 控制器类
 * @author 蔡繁荣 <fanrong33@qq.com>
 * @version 1.0.1 build 20170110
 */
class ReportAction extends Action{


    private function assign_date(){
        // 分别获取今天、昨天、最近7天、本月的时间
        $today_begin     = date('Y-m-d');
        $today_end       = date('Y-m-d');
        
        $yesterday_begin = date("Y-m-d", strtotime("-1 day"));
        $yesterday_end   = date("Y-m-d", strtotime("-1 day"));
        
        $week_begin      = date('Y-m-d', strtotime('-1 week'));
        $week_end        = date('Y-m-d');
        
        $month_begin     = date('Y-m-d', strtotime('-30 day'));
        $month_end       = date('Y-m-d');

        // 其他时间为默认本月到现在的时间区间
        $other_begin     = date('Y-m-01');
        $other_end       = date("Y-m-d");




        $this->assign('today_begin', $today_begin);
        $this->assign('today_end', $today_end);

        $this->assign('yesterday_begin', $yesterday_begin);
        $this->assign('yesterday_end', $yesterday_end);

        $this->assign('week_begin', $week_begin);
        $this->assign('week_end', $week_end);

        $this->assign('month_begin', $month_begin);
        $this->assign('month_end', $month_end);

        $this->assign('other_begin', $other_begin);
        $this->assign('other_end', $other_end);
    }


    public function index(){


        $this->assign_date();
        $this->display();
    }


    public function app(){


        $this->assign_date();
        $this->display();
    }


    public function placement(){


        $this->assign_date();
        $this->display();
    }


    /**
     * 按天获取日志统计信息列表接口
     * @param  integer  $offer_id            * 所属offerID
     * @param  string   $begin_date          添加开始日期
     * @param  string   $end_date            添加结束日期，当天23:59:59
     */
    public function get_summary_report_list(){

        if($this->isGet()){

            $begin_date = $this->_get('begin_date');
            $end_date   = $this->_get('end_date');
            

            // 1、安全验证，组装sql方式, 需要特别防止sql注入
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $begin_date)){
                $this->ajaxReturn('', '起始日期格式错误', 0);
            }
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)){
                $this->ajaxReturn('', '结束日期格式错误', 0);
            }
            
            // $diff = diff_between_two_days($begin_date, $end_date);
            // if($diff > 31){
            //     $this->ajaxReturn('', '间隔时间不能超过一个月', 0);
            // }


            import('@.ORG.Util.ChartsUtil');

            // $offer_id_str = ' AND offer_id='.$offer_id;
            if($begin_date && $end_date){
                $create_time_str = ChartsUtil::generate_time_str('timestamp', $begin_date, $end_date);
            }


            $day_list = ChartsUtil::generate_day_list($begin_date, $end_date);
            
            $model = M('Report');

            // 按天统计日期区间的销售数据
            $sql =<<<EOF
                select DATE_FORMAT(FROM_UNIXTIME(timestamp), '%Y-%m-%d') days, 
                sum(impressions) as impressions,
                sum(clicks) as clicks,
                sum(lead) as lead,
                sum(cpc) as cpc,
                sum(leadvalue) as leadvalue
                from t_report
                where 1=1 
                $create_time_str
                group by days
EOF;
            $data_list = $model->query($sql);
            
            

            // 统计汇总
            $total_impressions = 0;
            $total_clicks      = 0;


            $y_data_map = array();
            if($data_list){ // 有可能存在，也可能不存在数据
                foreach($data_list as $rs){

                    $y_data_map[$rs['days']] = $rs;


                    $total_impressions += $rs['impressions'];
                    $total_clicks      += $rs['clicks'];
                }
            }


            // 对数据进行安全验证，没有数据，则用0进行填补
            foreach ($day_list as $x_rs) {
                if(!isset($y_data_map[$x_rs])){
                    $y_data_map[$x_rs] = array(
                        'days'        => $x_rs,
                        'impressions' => 0,
                        'clicks'      => 0,
                        'lead'        => 0,
                        'cpc'         => 0.00,
                        'leadvalue'   => 0.00,
                    );
                }
            }
            ksort($y_data_map, SORT_NATURAL);


            $result = array(
                'list' => array_values($y_data_map),
                'stat' => array(
                    'total_impressions' => floatval($total_impressions),
                    'total_clicks'      => floatval($total_clicks),
                ),
            );
            $this->ajaxReturn($result, '获取流量汇总记录列表成功', 1);
        }else{
            $this->ajaxReturn('', '请求的HTTP METHOD不支持，请检查是否选择了正确的POST/GET方法', 0);
        }
    }


    /**
     * 按天获取日志统计信息列表接口
     * @param  integer  $offer_id            * 所属offerID
     * @param  string   $begin_date          添加开始日期
     * @param  string   $end_date            添加结束日期，当天23:59:59
     */
    public function get_app_report_list(){

        if($this->isGet()){

            $begin_date = $this->_get('begin_date');
            $end_date   = $this->_get('end_date');
            
            /** 模拟测试数据： 
            $begin_date = '2017-01-01';
            $end_date   = '2017-01-06';
            */


            // 1、安全验证，组装sql方式, 需要特别防止sql注入
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $begin_date)){
                $this->ajaxReturn('', '起始日期格式错误', 0);
            }
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)){
                $this->ajaxReturn('', '结束日期格式错误', 0);
            }
            
            // $diff = diff_between_two_days($begin_date, $end_date);
            // if($diff > 31){
            //     $this->ajaxReturn('', '间隔时间不能超过一个月', 0);
            // }


            import('@.ORG.Util.ChartsUtil');

            // $offer_id_str = ' AND offer_id='.$offer_id;
            if($begin_date && $end_date){
                $create_time_str = ChartsUtil::generate_time_str('timestamp', $begin_date, $end_date);
            }


            $day_list = ChartsUtil::generate_day_list($begin_date, $end_date);
            
            $model = M('Report');

            // 按天统计日期区间的报告数据
            $sql =<<<EOF
                select DATE_FORMAT(FROM_UNIXTIME(timestamp), '%Y-%m-%d') days, 
                sum(impressions) as impressions,
                sum(clicks) as clicks,
                sum(lead) as lead,
                sum(cpc) as cpc,
                sum(leadvalue) as leadvalue
                from t_report
                where 1=1 
                $create_time_str
                group by days
EOF;
            $data_list = $model->query($sql);
            
            

            // 统计汇总
            $total_impressions = 0;
            $total_clicks      = 0;


            $y_data_map = array();
            if($data_list){ // 有可能存在，也可能不存在数据
                foreach($data_list as $rs){

                    $y_data_map[$rs['days']] = $rs;


                    $total_impressions += $rs['impressions'];
                    $total_clicks      += $rs['clicks'];
                }
            }


            // 对数据进行安全验证，没有数据，则用0进行填补
            foreach ($day_list as $x_rs) {
                if(!isset($y_data_map[$x_rs])){
                    $y_data_map[$x_rs] = array(
                        'days'        => $x_rs,
                        'impressions' => 0,
                        'clicks'      => 0,
                        'lead'        => 0,
                        'cpc'         => 0.00,
                        'leadvalue'   => 0.00,
                    );
                }
            }
            ksort($y_data_map, SORT_NATURAL);


            // 按appId统计日期区间的报告数据
            $sql =<<<EOF
                select  
                appId,
                sum(impressions) as impressions,
                sum(clicks) as clicks,
                sum(lead) as lead,
                sum(cpc) as cpc,
                sum(leadvalue) as leadvalue
                from t_report
                where 1=1 
                $create_time_str
                group by appId
EOF;
            $app_report_list = $model->query($sql);


            $result = array(
                'list' => array_values($y_data_map),
                'app_list' => $app_report_list,
                'stat' => array(
                    'total_impressions' => floatval($total_impressions),
                    'total_clicks'      => floatval($total_clicks),
                ),
            );
            $this->ajaxReturn($result, '获取流量汇总记录列表成功', 1);
        }else{
            $this->ajaxReturn('', '请求的HTTP METHOD不支持，请检查是否选择了正确的POST/GET方法', 0);
        }
    }


    /**
     * 按天获取日志统计信息列表接口
     * @param  integer  $offer_id            * 所属offerID
     * @param  string   $begin_date          添加开始日期
     * @param  string   $end_date            添加结束日期，当天23:59:59
     */
    public function get_placement_report_list(){

        if($this->isGet()){

            $begin_date = $this->_get('begin_date');
            $end_date   = $this->_get('end_date');
            
            /** 模拟测试数据： 
            $begin_date = '2017-01-01';
            $end_date   = '2017-01-06';
            */


            // 1、安全验证，组装sql方式, 需要特别防止sql注入
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $begin_date)){
                $this->ajaxReturn('', '起始日期格式错误', 0);
            }
            if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)){
                $this->ajaxReturn('', '结束日期格式错误', 0);
            }
            
            // $diff = diff_between_two_days($begin_date, $end_date);
            // if($diff > 31){
            //     $this->ajaxReturn('', '间隔时间不能超过一个月', 0);
            // }


            import('@.ORG.Util.ChartsUtil');

            // $offer_id_str = ' AND offer_id='.$offer_id;
            if($begin_date && $end_date){
                $create_time_str = ChartsUtil::generate_time_str('timestamp', $begin_date, $end_date);
            }


            $day_list = ChartsUtil::generate_day_list($begin_date, $end_date);
            
            $model = M('Report');

            // 按天统计日期区间的报告数据
            $sql =<<<EOF
                select DATE_FORMAT(FROM_UNIXTIME(timestamp), '%Y-%m-%d') days, 
                sum(impressions) as impressions,
                sum(clicks) as clicks,
                sum(lead) as lead,
                sum(cpc) as cpc,
                sum(leadvalue) as leadvalue
                from t_report
                where 1=1 
                $create_time_str
                group by days
EOF;
            $data_list = $model->query($sql);
            
            

            // 统计汇总
            $total_impressions = 0;
            $total_clicks      = 0;


            $y_data_map = array();
            if($data_list){ // 有可能存在，也可能不存在数据
                foreach($data_list as $rs){

                    $y_data_map[$rs['days']] = $rs;


                    $total_impressions += $rs['impressions'];
                    $total_clicks      += $rs['clicks'];
                }
            }


            // 对数据进行安全验证，没有数据，则用0进行填补
            foreach ($day_list as $x_rs) {
                if(!isset($y_data_map[$x_rs])){
                    $y_data_map[$x_rs] = array(
                        'days'        => $x_rs,
                        'impressions' => 0,
                        'clicks'      => 0,
                        'lead'        => 0,
                        'cpc'         => 0.00,
                        'leadvalue'   => 0.00,
                    );
                }
            }
            ksort($y_data_map, SORT_NATURAL);


            // 按appId统计日期区间的报告数据
            $sql =<<<EOF
                select  
                placementId,
                sum(impressions) as impressions,
                sum(clicks) as clicks,
                sum(lead) as lead,
                sum(cpc) as cpc,
                sum(leadvalue) as leadvalue
                from t_report
                where 1=1 
                $create_time_str
                group by placementId
EOF;
            $placement_report_list = $model->query($sql);


            $result = array(
                'list'           => array_values($y_data_map),
                'placement_list' => $placement_report_list,
                'stat'           => array(
                    'total_impressions' => floatval($total_impressions),
                    'total_clicks'      => floatval($total_clicks),
                ),
            );
            $this->ajaxReturn($result, '获取流量汇总记录列表成功', 1);
        }else{
            $this->ajaxReturn('', '请求的HTTP METHOD不支持，请检查是否选择了正确的POST/GET方法', 0);
        }
    }


    /**
     * 导出整体报告到cvs
     */
    public function export_summary_cvs(){
        set_time_limit(0);

        $begin_date = $this->_get('begin_date');
        $end_date   = $this->_get('end_date');
        

        // 1、安全验证，组装sql方式, 需要特别防止sql注入
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $begin_date)){
            $this->ajaxReturn('', '起始日期格式错误', 0);
        }
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)){
            $this->ajaxReturn('', '结束日期格式错误', 0);
        }
        

        import('@.ORG.Util.ChartsUtil');

        $developer_id_str = ' AND developerId='.$this->_developer['id'];
        if($begin_date && $end_date){
            $create_time_str = ChartsUtil::generate_time_str('timestamp', $begin_date, $end_date);
        }


        
        $model = M('Report');

        // 按天统计日期区间的销售数据
        $sql =<<<EOF
            select DATE_FORMAT(FROM_UNIXTIME(timestamp), '%Y-%m-%d') days, 
            placementId,
            sum(impressions) as impressions,
            sum(clicks)      as clicks,
            sum(md_clicks)   as md_clicks,
            sum(lead)        as lead,
            sum(md_lead)     as md_lead,
            ''               as ctr,
            sum(cpc)         as cpc,
            sum(leadvalue)   as leadvalue
            from t_report
            where 1=1 
            $developer_id_str
            $create_time_str
            group by days, placementId
EOF;
        $data_list = $model->query($sql);
        

        // 对于开发者，默认只展示真实点击数量，对于运营增加一个参数叫做模拟点击展示比例，默认为0。
        // 如果为0.1则展示给开发者的总点击数则为：真实点击数量+模拟点击数量*0.1
        $tmp_data_list = array();
        foreach ($data_list as $key => $rs) {
            // 获取placement缓存信息
            $placement_config = get_placement_config($rs['placementId']);


            $clicks = floor($rs['clicks'] + $rs['md_clicks']*$placement_config['mdClickRatio']);
            $lead   = floor($rs['lead'] + $rs['md_lead']*$placement_config['mdLeadRatio']);

            if(isset($tmp_data_list[$rs['days']])){
                $rs['impressions'] = $tmp_data_list[$rs['days']]['impressions'] + $rs['impressions'];
                $rs['clicks']      = $tmp_data_list[$rs['days']]['clicks'] + $clicks;
                $rs['lead']        = $tmp_data_list[$rs['days']]['lead'] + $lead;
                $rs['cpc']         = $tmp_data_list[$rs['days']]['cpc'] + $rs['cpc'];
                $rs['leadvalue']   = $tmp_data_list[$rs['days']]['leadvalue'] + $rs['leadvalue'];
            }else{
                $rs['clicks'] = $clicks;
                $rs['lead']   = $lead;
            }
            unset($rs['placementId'], $rs['md_clicks'], $rs['md_lead']);
            $tmp_data_list[$rs['days']] = $rs;
        }
        $data_list = $tmp_data_list;
        unset($tmp_data_list);


        $y_data_map = array();
        if($data_list){ // 有可能存在，也可能不存在数据
            foreach($data_list as $rs){

                $rs['ctr']       = round($rs['clicks']/$rs['impressions']*100, 2).'%';
                $rs['cpc']       = sprintf('%.2f', $rs['cpc']);
                $rs['leadvalue'] = sprintf('%.2f', $rs['leadvalue']);


                $y_data_map[$rs['days']] = $rs;
            }
        }


        $day_list = ChartsUtil::generate_day_list($begin_date, $end_date);

        // 对数据进行安全验证，没有数据，则用0进行填补
        foreach ($day_list as $x_rs) {
            if(!isset($y_data_map[$x_rs])){
                $y_data_map[$x_rs] = array(
                    'days'        => $x_rs,
                    'impressions' => 0,
                    'clicks'      => 0,
                    'lead'        => 0,
                    'ctr'         => '0%',
                    'cpc'         => '0.00',
                    'leadvalue'   => '0.00',
                );
            }
        }
        ksort($y_data_map, SORT_NATURAL);


        $list = array_values($y_data_map);


        // 3、写入csv文件，并导出
        $dir = 'export/report/'.$this->_developer['id'].'/'.date('Ymd').'/';
        if(!is_dir($dir)){
            mkdir($dir, 0755, true);
        }

        $timestamp = time();
        $filename = 'report_summary_'.$timestamp.'_'.$begin_date.'_'.$end_date.'.csv';
        if(!is_file($dir.$filename)){

            $fp = fopen($dir.$filename, 'a');

            // 输出Excel列名信息
            $head = array("days", "impressions", 'clicks', 'lead', 'ctr', 'cpc', 'leadvalue');
            foreach ($head as $k => $v) {
                // CSV的Excel支持GBK编码，一定要转换，否则乱码
                $head[$k] = mb_convert_encoding(trim($v), 'gbk', 'utf-8');
            }

            // 将数据通过fputcsv写到文件句柄
            fputcsv($fp, $head);
        }else{
            $fp = fopen($dir.$filename, 'a');
        }




        // 计数器
        $number = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 2000; // 100000

        // 逐行取出数据，不浪费内存
        $count = count($list);

        for($j=0; $j<$count; $j++) {

            $number ++;
            if ($limit == $count) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $number = 0;
            }
            $row[] = $list[$j];
            

            foreach ($row as $i => $v) {
                // $row[$i] = mb_convert_encoding(trim($v),'gbk','utf-8');
                fputcsv($fp, $row[$i]);
            }
            unset($row);
        }
        fclose($fp);


        import('@.ORG.Net.Http');
        Http::download($dir.$filename);
    }
    

}

?>