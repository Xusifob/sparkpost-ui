<?php



namespace Xusifob\Sparkpost\Service;


/**
  *
 * @author Xusifob <b.malahieude@free.fr>
 *
 * @package Xusifob\Sparkpost\Service
 *
 * Get bunch of useful methods
 *
 * Class Utils
 */
abstract class Utils
{


    /**
     *
     * Return a parameter from
     *
     * @param array|string $array
     * @param array $parameters
     *
     * @return null|string
     */

    public static function extractFromRequest($array,$parameters = array())
    {

        if(is_string($array)){
            $array = array($array);
        }

        if(empty($parameters)){
            $parameters = $_GET;
        }

        $array = array_map('strtolower', $array);


        foreach($parameters as $key => $value){
            if(in_array(strtolower($key),$array) && !empty($value)){
                return $value;
                break;
            }
        }

        return null;
    }

    /**
     *
     * Sort the metrics data
     *
     * @param $a
     * @param $b
     * @return int
     */
    public static function metrics_sort($a,$b)
    {
        if ($a['count_unique_clicked'] == $b['count_unique_clicked'])
        {

            if ($a['count_unique_rendered'] == $b['count_unique_rendered']){
                return $a['count_accepted'] < $b['count_accepted']  ? 1 : -1;

            }

            return $a['count_unique_rendered'] < $b['count_unique_rendered']  ? 1 : -1;
        }

        return $a['count_unique_clicked'] < $b['count_unique_clicked'] ? 1 : -1;
    }




    /**
     * @param $metric
     * @param int $dec
     * @return mixed
     */
    public static function add_rates($metric,$dec = 0)
    {

        $dec += 2;

        $metric['CTR'] = 0 == $metric['count_unique_rendered'] ? 0 : number_format($metric['count_unique_clicked']/$metric['count_unique_rendered'],$dec)*100;
        $metric['OR']  = 0 == $metric['count_accepted'] ? 0 : number_format($metric['count_unique_rendered']/$metric['count_accepted'],$dec)*100;
        $metric['AR']  = 0 == $metric['count_targeted'] ? 0 : number_format($metric['count_accepted']/ $metric['count_targeted'],$dec)*100;

        return $metric;
    }


    /**
     *
     * Build a link from
     *
     * @param $link
     * @param $campaign_id
     * @param $campaign_source
     * @param $unsubscribe_link
     *
     * 
     * @return string
     */
    public static function build_link($link,$campaign_id,$campaign_source,$unsubscribe_link)
    {

        $link = str_replace(array('"','href='),array('',''),$link);

        if(preg_match('/(\*\|UNSUB\|\*|{{unsubscribe}})/i',$link)){
            $link = $unsubscribe_link;
        }

        $exploded = explode('?',html_entity_decode($link));

        $link = $exploded[0];

        $output =  array();

        if(isset($exploded[1])){
            parse_str($exploded[1],$output);
        }

        $output['utm_medium'] = 'email';
        $output['utm_campaign'] = isset($_POST['campaign_id']) ? urlencode($campaign_id) : '';
        $output['utm_source'] = isset($_POST['campaign_source']) ? urlencode($campaign_source) : '';
        $output['utm_content'] = '{{email}}';

        $link .= '?' . http_build_query($output);


        $link = str_replace(array('%7B','%7D'),array('{','}'),$link);

        return 'href="' . $link . '"';

    }






    /**
     * @param $csvFile
     * @return mixed
     */
    public static function detectDelimiter($csvFile)
    {
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        );

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }



    /**
     *
     * Transform an array to a CSV file
     *
     * @param $data
     * @param string $filename
     * @param string $delimiter
     */
    public static function array_to_csv($data, $filename = 'export.csv',$delimiter = ';')
    {
        $exist = false;

        if(file_exists($filename)){
            $exist = true;
        }

        $output = fopen($filename, 'a+');


        if(!$exist) {
            $header = array_keys( $data[0] );
            fputcsv( $output, $header,$delimiter );
        }


        foreach ($data as $row) {
            fputcsv($output, $row,$delimiter);
        }
        rewind($output);
        $data = '';
        while ($line = fgets($output)) {
            $data .= $line;
        }
        $data .= fgets($output);

        fclose($output);
        //chmod($filename, 0777);
    }




    /**
     * @param string $filename
     * @param string $delimiter
     *
     * @return array
     */
    public static function csv_to_array($filename = 'data.csv',$delimiter = ';')
    {

        $csv_data = file_get_contents($filename);

        $delimiter = Utils::detectDelimiter($filename);

        $lines = explode("\n", $csv_data);
        $head = str_getcsv(array_shift($lines),$delimiter);

        $array = array();
        foreach ($lines as $line) {

            $csv = str_getcsv($line,$delimiter);

            if(count($head) == count($csv)) {
                $array[] = array_combine( $head, $csv );
            }
        }

        return $array;
    }



}