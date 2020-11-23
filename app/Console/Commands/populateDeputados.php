<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deputado;
use App\Models\listaTelefonica;
use App\Models\SocialMedias;

class populateDeputados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate-deputados {type : type name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popula a tabela de deputados';

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

      if ($type == 'get-currents') {
          $this->getBasico();
      }

      if ($type == 'get-data') {
          $this->getData();
      }
    }

    protected function getData () {
      $response = $this->initCurl($_ENV['API_URL'] . 'deputados/lista_telefonica');

      if (!empty($response)) {
        $params = [
          'id',
          'nomeServidor',
          'nome',
          'partido',
          'endereco',
          'telefone',
          'fax',
          'email',
          'naturalidadeMunicipio',
          'naturalidadeUf',
          'dataNascimento'
        ];

        foreach ($response['contato'] as $value) {
          foreach ($params as $param) {
            if (!array_key_exists($param, $value)) {
              $value[$param] = '';
            }
          }

          $lista = new listaTelefonica();
          $lista->code = $value['id'];
          $lista->name = $value['nomeServidor'];
          $lista->alias = $value['nome'];
          $lista->party = $value['partido'];
          $lista->address = $value['endereco'];
          $lista->phone = $value['telefone'];
          $lista->fax = $value['fax'];
          $lista->email = $value['email'];
          $lista->born_city = $value['naturalidadeMunicipio'];
          $lista->born_state = $value['naturalidadeUf'];

          $date = array_reverse(explode('/', $value['dataNascimento']));
          if (!empty($date)) {
            $date[0] = str_pad(preg_replace('/\D/', '', $date[0]), 2, '0', STR_PAD_LEFT);
            $date[1] = str_pad(preg_replace('/\D/', '', $date[1]), 2, '0', STR_PAD_LEFT);
            $date[2] = preg_replace('/\D/', '', $date[2]);
          }

          $date = implode('-', $date);
          $lista->birth = $date;
          $lista->save();

          //importante: este endpoint não retornou nenhum deputado
          // com alguma rede social válida
          if (isset($value['redesSociais']) && is_array($value['redesSociais'])) {
            foreach ($value['redesSociais'] as $item) {
              $social = new SocialMedias();
              $social->code = $value['id'];
              $social->name = $item['nome'];
              $social->url = $item['url'];
              $social->save();
            }
          }
        }
      }

    }

    protected function getcurrents () {
      $response = $this->initCurl($_ENV['API_URL'] . 'deputados/em_exercicio');

      if (!empty($response['deputado'])) {
        foreach ($response['deputado'] as $value) {
          $deputado = new Deputado();
          $deputado->name = $value['nome'];
          $deputado->code = $value['id'];
          $deputado->partido = $value['partido'];
          $deputado->tag_localizacao = $value['tagLocalizacao'];
          $deputado->save();
        }
      }

      return 0;
      curl_close($curl);
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
