<?php


namespace Xusifob\Sparkpost\Service;

use \GuzzleHttp\Client;
use Xusifob\Sparkpost\Http\CSVResponse;
use Xusifob\Sparkpost\Service\Utils;
use ForceUTF8\Encoding;


/**
 *
 * @author Xusifob <b.malahieude@free.fr>
 *
 * @package Xusifob\Sparkpost\Service
 *
 * This class handles all sparkpost calls
 *
 * Class SparkPost
 */
class SparkPost
{


    /**
     *
     * The Sparkpost API Key
     *
     * @var
     */
    protected $api_key;


    /**
     * @var Client;
     */
    protected $client;


    /**
     * @var string
     */
    protected $unsubscribe_link;


    /**
     * @var array
     */
    protected  $total = array(
        'count_targeted' => 0,
        'count_accepted' => 0,
        'count_unique_clicked' => 0,
        'count_unique_rendered' => 0,
        'AR' => 0,
        'OR' => 0,
        'CTR' => 0,
    );




    /**
     * Sparkpost constructor.
     * @param array     $config   The Sparkpost configs
     */
    public function __construct($config = array())
    {

        $this->api_key = $config['api_key'];
        $this->unsubscribe_link = $config['unsubscribe_link'];


        $this->client = new Client(array(
            'base_uri' => 'https://api.sparkpost.com/api/v1/',
            'headers' =>  array(
                'Content-Type' => 'application/json',
                'Authorization' => $this->api_key,
            ),
        ));

    }


    /**
     * @return int
     */
    public function getTimestamp()
    {
        return strtotime('2 week ago');
    }



    /**
     * @return array
     */
    public function getCampaigns()
    {

        $campaigns = json_decode($this->client->get('metrics/campaigns',array(
            'query' => array(
                'from' => date('Y-m-d',$this->getTimestamp()) . 'T' . date('H:i',$this->getTimestamp()),
                'limit' => 9999
            ),
        ))->getBody()->getContents(),true);



        sort($campaigns['results']['campaigns']);


        return $this->removeTests($campaigns);


    }

    /**
     * @return array
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param array $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }


    /**
     * @return mixed
     */
    public function getTemplates()
    {
        return json_decode($this->client->get('templates')->getBody()->getContents(),true);
    }




    /**
     * @return mixed
     */
    public function getMessageEvents()
    {
        return json_decode($this->client->get('message-events',array(
            'query' => array(
                'from' => date('Y-m-d',$this->getTimestamp()) . 'T' . date('H:i',$this->getTimestamp()),
                'per_page' => 9999,
                'events' => implode(',',
                    array('delivery',
                        'bounce',
                        'open',
                        'click',
                        'list_unsubscribe',
                        'link_unsubscribe',
                        'policy_rejection',
                        'out_of_band',
                        'generation_rejection'
                    )
                ),
                'campaign_ids' =>  implode(',',$_GET['campaign_metrics']),
            )
        ))->getBody()->getContents(),true);
    }



    /**
     * Remove test campains
     *
     * @param $campaigns
     * @return array
     */
    protected function removeTests($campaigns)
    {
        foreach($campaigns['results']['campaigns'] as $key => $campaign)
        {


            if(preg_match('/^test/i',$campaign)){
                unset($campaigns['results']['campaigns'][$key]);
            }
        }



        return $campaigns;
    }


    /**
     * @param $events
     * @param $recipients
     */
    public function sortEvents($events,$recipients)
    {
        foreach($events['results'] as $event)
        {

            $empty_recipient = array(
                'email' => '',
                'count_targeted' => 0,
                'count_accepted' => 0,
                'count_unique_rendered' => 0,
                'count_unique_clicked' => 0,
                'unsubscribe' => false,
                'domain' => '',
                'url' => '',
            );


            $email = $event['raw_rcpt_to'];
            $domain = explode('@',$email)[1];

            $recipient = isset($recipients[$domain][$email]) ? $recipients[$domain][$email] : $empty_recipient;


            switch($event['type']){
                case 'open':
                    $recipient['count_unique_rendered']++;
                    break;
                case 'delivery':
                    $recipient['count_targeted']++;
                    $recipient['count_accepted']++;
                    break;
                case 'bounce':
                case 'policy_rejection':
                case 'generation_rejection':
                    $recipient['count_targeted']++;
                    break;
                case 'out_of_band':
                    $recipient['count_accepted']--;
                    break;
                case 'click':
                    if(preg_match('/unsubscribe/',$event['target_link_url'])){
                        $recipient['unsubscribe'] = true;
                    }else{
                        $recipient['count_unique_clicked']++;
                        $recipient['url'] = $event['target_link_url'] ;
                    }
                    break;
                case 'list_unsubscribe' :
                case 'link_unsubscribe' :
                    $recipient['unsubscribe'] = true;
                    break;
            }

            $recipient['domain'] = $domain;
            $recipient['email'] = $email;

            $recipient = Utils::add_rates($recipient);


            $recipients[$domain][$email] = $recipient;

        }

        return $recipients;
    }


    /**
     * @return mixed
     */
    public function getMetrics()
    {
        return json_decode($this->client->get('metrics/deliverability/domain',array(
            'query' => array(
                'campaigns' => implode(',',$_GET['campaign_metrics']),
                'from' => date('Y-m-d',$this->getTimestamp()) . 'T' . date('H:i',$this->getTimestamp()),
                'metrics' => 'count_accepted,count_unique_clicked,count_unique_rendered,count_targeted',
                'limit' => 9999
            ),
        ))->getBody()->getContents(),true)['results'];

    }


    public function sortMetrics($recipients,$metrics)
    {
        foreach($metrics as $key =>  $metric){
            if($metric['domain'] == 'dunforce.io' ){
                unset($metrics[$key]);
                continue;
            }



            // Remove unsubscribe link
            if(isset($recipients[$metric['domain']])){
                foreach($recipients[$metric['domain']] as $email => $recipient){
                    if($recipient['unsubscribe']){
                        $metric['count_unique_clicked']--;
                    }
                }
            }


            $metric = Utils::add_rates($metric);





            $this->total['count_targeted']+= $metric['count_targeted'];
            $this->total['count_accepted']+= $metric['count_accepted'];
            $this->total['count_unique_clicked']+= $metric['count_unique_clicked'];
            $this->total['count_unique_rendered']+= $metric['count_unique_rendered'];


            $metrics[$key] = $metric;

        }


        $this->total = Utils::add_rates($this->total,2);

        usort($metrics,array('Xusifob\Sparkpost\Service\Utils','metrics_sort'));

        return $metrics;

    }


    /**
     * @param $recipients
     */
    public function export($recipients)
    {
        $to_export = array();

        foreach($recipients as $re){
            foreach($re as $r){
                $to_export[] = $r;

            }
        }


        $response = new CSVResponse(array());

        $response->setDelimiter(';');

        $response->setData($to_export);

        $response->send();
    }


    /**
     *
     * return Sparkpost HTML Template
     *
     * @param $template_id
     * @return mixed
     */
    public function getTemplateHTML($template_id)
    {
        $response = $this->client->get('templates/'. $template_id .'?draft=true');


        $r =  json_decode($response->getBody()->getContents(),true);


        return $r;


    }


    /**
     *
     * Unsubscribe a contact
     *
     * @param string    $email
     * @param string    $type
     * @return string
     */
    public function unsubscribe($email,$type = "non_transactional")
    {
        $response = $this->client->put('suppression-list/',array(
            'json' => array(
                'recipients' => array(
                    array(
                        'recipient' => $email,
                        'type' => $type
                    )
                )
            )
        ));

        return json_decode($response->getBody()->getContents(),true);

    }



    /**
     * @param $html
     * @param string $campaign_id
     * @param string $campaign_source
     * @return mixed
     */
    public function updateHTML($html,$campaign_id = '',$campaign_source = '')
    {

        preg_match_all('#href="[^"]+"#i',$html,$links);

        foreach($links[0] as $link){
            $new_link = Utils::build_link($link,$campaign_id,$campaign_source,$this->unsubscribe_link);
            $html = str_replace($link,$new_link,$html);
        }


        preg_match_all('#[^"\'><]https?:\/\/[^" \n]+#i',$html,$links);

        foreach($links[0] as $link){
            $first = $link[0];
            $link = substr($link, 1);

            $new_link = Utils::build_link($link,$campaign_id,$campaign_source,$this->unsubscribe_link);

            $html = str_replace($link,'<a '. $new_link .' >'. $link .'</a>',$html);
        }

        return $html;
    }




    /**
     * @param $recipients
     * @param $template_id
     * @param $campaign_id
     * @return string
     */
    public function sendEmail($recipients,$template_id,$campaign_id)
    {


        $data = array(
            'campaign_id' => trim($campaign_id),
            'recipients' => $recipients,
            "content" => array(
                "template_id" => $template_id,
                "use_draft_template" => true
            ),

            "substitution_data" =>  array(
                'campaign_id' => trim(urlencode($campaign_id)),
                'unsubscribe_link' => $this->unsubscribe_link,
                'unsubscribe' => $this->unsubscribe_link,
            )
        );


        $response = $this->client->post('transmissions', array(
            'json' => $data,
        ));

        return $response->getBody()->getContents();

    }




    public function updateTemplate($template_id,$html,$r)
    {

        $this->client->put('templates/' . $template_id,array(
            'json' => array(
                'content' => array(
                    "from" => $r['results']['content']['from'],
                    "subject" => $r['results']['content']['subject'],
                    'html' => $html,
                )
            )
        ));

    }




    public function createSparkpostList()
    {

        $data = Utils::csv_to_array($_FILES['file']['tmp_name']);

        $q = array();



        foreach($data as $d)
        {

            $first_name = Utils::extractFromRequest(array(
                'first_name',
                'First Name',
                'prénom',
            ),$d);

            $last_name = Utils::extractFromRequest(array(
                'last_name',
                'Last Name',
                'name',
                'nom'
            ),$d);

            $company = Utils::extractFromRequest(array(
                'entreprise',
                'company',
                'Société'
            ),$d);


            $dso = Utils::extractFromRequest(array(
                'DSO',
            ),$d);


            $email = Utils::extractFromRequest(array(
                'Email(Work)',
                'Email(Personal)',
                'Email(default)',
                'email',
                'mail'
            ),$d);


            if(!$email){
                continue;
            }


            $q[] = array(
                'email' => trim($email),
                'name' => Encoding::fixUTF8(ucfirst(strtolower($first_name))) . ' ' . Encoding::fixUTF8(ucfirst(strtolower($last_name))),
                'return_path' => 'bastien+bounce@dunforce.io',
                'metadata' => '',
                'substitution_data' => json_encode(array_merge($d,array(
                    'email' => Encoding::fixUTF8(trim($email)),
                    "DSO" => Encoding::fixUTF8(ucfirst(strtolower($dso))),
                    "company" => Encoding::fixUTF8(ucfirst(strtolower($company))),
                    "first_name" => Encoding::fixUTF8(trim(ucfirst(strtolower($first_name)))),
                    "last_name" => Encoding::fixUTF8(trim(ucfirst(strtolower($last_name)))),

                ))),
                'tags' => ''
            );


        }


        $response = new CSVResponse($q);
        $response->setFilename('export-' . $_FILES['file']['name']);
        $response->send();
        die();
    }


    /**
     *
     * Update the link
     *
     * @param $template_id
     * @param $campaign_id
     * @param $campaign_source
     */
    public function UpdateTemplateLinks($template_id,$campaign_id,$campaign_source)
    {

        $r = $this->getTemplateHTML($template_id);
        $html = $this->updateHTML($r['results']['content']['html'],$campaign_id,$campaign_source);
        $this->updateTemplate($template_id,$html,$r);

    }




    /**
     *
     * Return the list of recipient
     *
     * @return mixed
     */
    public function getRecipientList()
    {
        return  json_decode($this->client->get('recipient-lists')->getBody()->getContents(),true);
    }

}