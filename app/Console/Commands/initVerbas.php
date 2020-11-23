<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deputado;
use App\Models\RefundDates;
use App\Models\Refunds;

class initVerbas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init-verbas {type : type name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consome as verbas idenizatorias da almg';

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
     * @return int
     */
    public function handle()
    {
        $type = $this->argument('type');

        if ($type == 'get-dates') {
            $this->getDates();
        }

        if ($type == 'get-refunds') {
            $this->getRefunds();
        }

        return 1;
    }

    protected function getDates ()
    {
        $deputado = new Deputado();
        $deputados = $deputado->all();

        if (!empty($deputados)) {
            foreach ($deputados as $value) {
                $code = $value->code;
                $uri = "prestacao_contas/verbas_indenizatorias/legislatura_atual/deputados/$code/datas";
                $response = $this->initCurl($_ENV['API_URL'] . $uri);

                if (!empty($response)) {
                    $array = array();
                    foreach ($response as $resp) {
                        if (count($resp) > 2) {
                            foreach ($resp as $resp_array) {
                                if ($resp_array['idDeputado'] && $resp_array['dataReferencia']) {
                                    $aux = explode('-', $resp_array['dataReferencia']);
                                    $ref = $aux[0] . '-' . $aux[1];
                                    if (!isset($array[$resp_array['idDeputado']])) {
                                        $array[$resp_array['idDeputado']] = [];
                                    }

                                    if (!in_array($ref, $array[$resp_array['idDeputado']])) {
                                        array_push($array[$resp_array['idDeputado']], $ref);
                                    }
                                }
                            }
                        } else {
                            if ($resp['idDeputado'] && $resp['dataReferencia']) {
                                $aux = explode('-', $resp['dataReferencia']);
                                $ref = $aux[0] . '-' . $aux[1];
                                if (!isset($array[$resp['idDeputado']])) {
                                    $array[$resp['idDeputado']] = [];
                                }

                                if (!in_array($ref, $array[$resp['idDeputado']])) {
                                    array_push($array[$resp['idDeputado']], $ref);
                                }
                            }
                        }
                    }
                    if (!empty($array)) {
                        foreach ($array as $key => $el) {
                            foreach ($el as $ref) {
                                $dates = new RefundDates();
                                $dates->code = $key;
                                $ref = explode('-', $ref);
                                $dates->month = $ref[1];
                                $dates->year = $ref[0];
                                $dates->save();
                            }
                        }
                    }
                }
                sleep(1);
            }
        }
    }

    protected function getRefunds ()
    {
        $refDates = new RefundDates();
        $refDates = $refDates->all();

        if (!empty($refDates)) {
            foreach ($refDates as $value) {
                $code = $value->code;
                $month = $value->month;
                $year = $value->year;
                $uri = "prestacao_contas/verbas_indenizatorias/legislatura_atual/deputados/$code/$year/$month";
                $response = $this->initCurl($_ENV['API_URL'] . $uri);

                if (!empty($response)) {
                    if (isset($response['resumoVerba'])) {
                        $resumo = $response['resumoVerba'];
                        if (!empty($resumo)) {
                            $sum = 0;
                            foreach ($resumo as $resumo) {
                                if (isset($resumo['valor'])) {
                                    $resumo['valor'] = str_replace('.', '', $resumo['valor']);
                                    $resumo['valor'] = str_replace(',', '.', $resumo['valor']);
                                    $sum = $sum + $resumo['valor'];
                                }
                            }
                            $refund = new Refunds();
                            $refund->description = isset($resumo['descTipoDespesa']) ? $resumo['descTipoDespesa'] : '';
                            $refund->code = $code;
                            $refund->month = $month;
                            $refund->year = $year;
                            $refund->value = $sum;
                            $refund->save();
                        }
                    }
                }
                sleep(1);
            }
        }

    }

    protected function initCurl ($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Cookie: TS0172020d=0122c241b3b9ddb0ff03c825d5ca188c55ae64f4982b5063db3e2ea3a6fdbfbeb919e0713b5dc75a48e4b616aff218f67f1a556b5b"
            ),
        ));

        $response = curl_exec($curl);

        if (curl_error($curl)) {
            print_r(curl_error($curl));
            curl_close($curl);
            return 0;
        }

        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        return $array;
    }
}
