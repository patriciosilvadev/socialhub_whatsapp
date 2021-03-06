<?php

namespace App\Business;

use App\Http\Controllers\ContactsOriginsController;
use App\Http\Controllers\ExternalRPIController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackingController;
use App\Jobs\SendWhatsAppMsgTracking;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Status;
use App\Models\Tracking;
use App\Repositories\TrackingRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use stdClass;
use Illuminate\Support\Carbon;

class TrackingBusiness extends Business
{

    public function __construct()
    {
        parent::__construct();

        $this->repo = new TrackingRepository(app());
    }

    public function getNewTrackingMessage(Tracking $Tracking, int $company_id): string
    {
        try {
            $message = "";

            $Company = Company::with('rpi')->find($company_id);

            $trackingList = json_decode($Tracking->tracking_list) ?? array();

            $newTrackingList = $this->processTrackingObject($Tracking, $Company);

            if (count($newTrackingList) > count($trackingList)) {
                $message = $this->builTrackingMessage(json_decode($Tracking->json_csv_data), $newTrackingList[0], $Company);
                $aux = json_encode($newTrackingList);
                $Tracking->tracking_list = json_encode($newTrackingList);

                if (in_array($newTrackingList[0]->tipo, ['EST', 'LDI', 'BLQ', 'BDE', 'BDI', 'BDR'])) {
                    $Tracking->status_id = TrackingController::TRACKING_RECEIVED;
                }
            }

            return $message ?? "";
        } catch (\Throwable $th) {
            //throw $th;
        }

        return "";
    }

    public function initCorreios()
    // public function initCorreios(\PhpSigep\Model\AccessData $accessData)
    {
        // $accessData = new \PhpSigep\Model\AccessDataHomologacao();
        
        $this->config = new \PhpSigep\Config();
        $this->config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
        // $this->config->setEnv(\PhpSigep\Config::ENV_DEVELOPMENT);
        $this->config->setCacheOptions(
            array(
                'storageOptions' => array(
                    // Qualquer valor setado neste atributo será mesclado ao atributos das classes
                    // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                    // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                    'enabled' => false,
                    'ttl' => 20, // "time to live" de 10 segundos
                    'cacheDir' => sys_get_temp_dir(), // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                ),
                )
            );
            
        \PhpSigep\Bootstrap::start($this->config);
    }

    public function CorreiosTrackingObject(Company $Company, string $tracking_code)
    {
        $usuario = "2689761400"; // $Company->tracking_user;
        $senha = "H1OR;3@Y@M"; // $Company->tracking_pass;
        $cnpjEmpresa = '26897614000101';
        $numcontrato = '9912467470';
        $codigoadm = '19185251';
        $cartaopostagem = '0074969366';

        $this->initCorreios();


        $accessData = new \PhpSigep\Model\AccessDataHomologacao();

        $accessData->setUsuario($usuario);
        $accessData->setSenha($senha);
        $accessData->setCodAdministrativo(null);

        $this->config->setAccessData($accessData);
        // $accessData->setCnpjEmpresa($cnpjEmpresa);
        // $accessData->setCodAdministrativo($codigoadm);
        // $accessData->setNumeroContrato($numcontrato);
        // $accessData->setCartaoPostagem($cartaopostagem);
        // $accessData->setAnoContrato(null);
        // $accessData->setDiretoria(new \PhpSigep\Model\Diretoria(\PhpSigep\Model\Diretoria::DIRETORIA_DR_SAO_PAULO));

        // $this->initCorreios($accessData);

        // $accessData = new \PhpSigep\Model\AccessDataHomologacao();

        // Solicita as etiquetas
        // $dados_etiquetas = new \PhpSigep\Model\SolicitaEtiquetas();
        // $dados_etiquetas->setAccessData($this->config->getAccessData());
        // $dados_etiquetas->setQtdEtiquetas(1);

        // $dados_etiqueta->setServicoDePostagem(\PhpSigep\Model\ServicoDePostagem::SERVICE_PAC_41068);
        $etiqueta = new \PhpSigep\Model\Etiqueta();
        Log::debug('CorreiosTrackingObject', [$tracking_code]);
        $etiqueta->setEtiquetaComDv($tracking_code);

        $params = new \PhpSigep\Model\RastrearObjeto();
        $params->setAccessData($this->config->getAccessData());
        $params->setEtiquetas([$etiqueta]);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->rastrearObjeto($params);

        return $result;
    }

    public function processTrackingObject(Tracking $Tracking, Company $Company)
    {
        try {
            $newTrackingList = array();

            $response = $this->CorreiosTrackingObject($Company, $Tracking->tracking_code);
            Log::debug("processTrackingObject $Tracking->tracking_code", [$response->getResult()]);

            if ($response && count($response->getResult())) {
                $eventList = $response->getResult()[0]->getEventos();

                foreach ($eventList as $key => $event) {
                    $newTrackingList[$key] = (object) $event->toArray();
                }

                // Check whether last even need action
                $POB = new \App\Business\PostofficeBusiness();
                if (count($eventList) && (
                            // se esta na lista de codigos de erros
                            in_array([$newTrackingList[0]->tipo, $newTrackingList[0]->status], $POB->trackingImportantEventList())
                            // ou se está como encaminhado mas com mais de 10 dias 
                        ||  (   in_array([$newTrackingList[0]->tipo, $newTrackingList[0]->status], $POB->trackingImportantEventListSended()) 
                                && Carbon::now()->diffInDays(Carbon::parse($newTrackingList[0]->dataHora)) > 10 
                            )
                    )) {
                    Log::debug("processTrackingObject TRACKING_PROBLEM", [$Tracking]);
                    
                    $Tracking->status_id = TrackingController::TRACKING_PROBLEM;
                    $Tracking->save();
                }
            }

            return $newTrackingList;
        } catch (\Throwable $tr) {
            // throw $tr;
        }
        
    }

    public function createTrackingJob(Tracking $Tracking, Company $Company): bool
    {
        try {
            $ExternalRPIController = new ExternalRPIController($Company->rpi);
            $Contact = Contact::find($Tracking->contact_id);

            if ($Contact) {
                $Tracking = (object) $Tracking->toArray();
                SendWhatsAppMsgTracking::dispatch($ExternalRPIController, $Contact, $Tracking, 'tracking_update');
            } else {
                throw new \Exception("createTrackingJob Contact($Tracking->contact_id) not found in Tracking ($Tracking->id)");
            }
        } catch (\Throwable $tr) {
            Log::debug('TrackingsBussines createTracking Job', [$tr]);
            return false;
        }

        return true;
    }

    public function createTracking(stdClass $Tracking, Company $Company): string
    {
        try {
            // 1. Crea la tracking
            // Check if the Tracking already exist for this company
            if ($Tracking && isset($Tracking->pedidoID)) {
                $TrackingModel = new Tracking();
                $TrackingModel->table = "$Company->id";
                $TrackingModel = $TrackingModel->find($Tracking->pedidoID);
                // dd($TrackingModel);
                if (!$TrackingModel) { // if not exist insert the Tracking
                    // 2. Crea el contacto si no existe
                    $Contact = new Contact();
                    $Contact->company_id = $Company->id;
                    $Contact->first_name = trim($Tracking->compradorNome) ?? trim($Tracking->compradorCPF);
                    // $Contact->whatsapp_id = $Tracking->pedidoID;
                    $Contact->email = '';
                    $Contact->origin = ContactsOriginsController::CORREIOS;
                    if (isset($Tracking->compradorFone) && $Tracking->compradorFone) {
                        $phone = $Tracking->compradorFone;
                        $phone = preg_replace("/[^0-9]/", "", $phone);
                        if (!(strpos('55', $phone) === 0)) {
                            $phone = "55$phone";
                        }

                        // Check if the Contact already exist for this company
                        $foundContact = Contact::where([
                            'company_id' => $Company->id,
                            'whatsapp_id' => $phone,
                        ])->first();

                        if ($foundContact) { // if not exist insert the contact
                            $Contact = $foundContact;
                        } else {
                            $Contact->whatsapp_id = $phone;
                            $Contact->save();
                        }
                    } else {
                        $Contact->save();
                    }

                    try {
                        // var_dump($Tracking);
                        $TrackingModel = Tracking::trackingConstruct($Tracking, $Contact->id, $Company->id);
                        $TrackingModel->save();
                    } catch (\Throwable $tr) {
                        return 'exception';
                    }
                    Log::error('Trackings Bussines createTracking', [$Contact->whatsapp_id]);
                    return 'criated';
                } else {
                    return 'already_exist';
                }
            }
        } catch (\Throwable $tr) {
            Log::debug('TrackingsBussines createTracking', [$tr]);
            throw $tr;
            return 'exception';
        }

        return true;
    }

    public function builTrackingMessage(stdClass $Tracking, stdClass $Event, Company $Company): string
    {

        $replace = [
            $Tracking->compradorNome ?? '@compradorNome',
            $Tracking->compradorApelido ?? '@compradorApelido',
            $Tracking->compradorEmail ?? '@compradorEmail',
            $Tracking->compradorFone ?? '@compradorFone',
            $Tracking->compradorCPF ?? '@compradorCPF',
            $Tracking->compradorRG ?? '@compradorRG',

            $Tracking->enderecoRua ?? '@enderecoRua',
            $Tracking->enderecoNumero ?? '@enderecoNumero',
            $Tracking->enderecoComplemento ?? '@enderecoComplemento',
            $Tracking->enderecoBairro ?? '@enderecoBairro',
            $Tracking->enderecoCidade ?? '@enderecoCidade',
            $Tracking->enderecoEstado ?? '@enderecoEstado',
            $Tracking->enderecoCep ?? '@enderecoCep',

            $Tracking->pagamentoStatus ?? '@pagamentoStatus',
            $Tracking->pagamentoForma ?? '@pagamentoForma',

            $Tracking->pedidoID ?? '@pedidoID',
            $Tracking->pedidoTotalProd ?? '@pedidoTotalProd',
            $Tracking->pedidoTotalFrete ?? '@pedidoTotalFrete',
            $Tracking->pedidoStatus ?? '@pedidoStatus',
            $Tracking->pedidoData ?? '@pedidoData',
            $Tracking->pedidoObservacoes ?? '@pedidoObservacoes',

            $Tracking->envioTransportadora ?? '@envioTransportadora',
            $Tracking->envioRastreamento ?? '@envioRastreamento',
            $Tracking->envioData ?? '@envioData',

            $Tracking->entregaData ?? '@entregaData',
            $Tracking->origem ?? '@origem',
            $Tracking->conta ?? '@conta',
            $Tracking->sku ?? '@sku',

            $Event->tipo ?? '@tracking_tipo',
            $Event->status ?? '@tracking_status',
            $Event->dataHora ?? '@tracking_dataHora',
            $Event->descricao ?? '@tracking_descricao',
            $Event->recebedor ?? '@tracking_recebedor',
            $Event->detalhe ?? '@tracking_detalhe',
            $Event->local ?? '@tracking_local',
            $Event->codigo ?? '@tracking_codigo',
            $Event->cidade ?? '@tracking_cidade',
            $Event->uf ?? '@tracking_uf',
        ];

        $search = [
            '@compradorNome',
            '@compradorApelido',
            '@compradorEmail',
            '@compradorFone',
            '@compradorCPF',
            '@compradorRG',
            '@enderecoRua',
            '@enderecoNumero',
            '@enderecoComplemento',
            '@enderecoBairro',
            '@enderecoCidade',
            '@enderecoEstado',
            '@enderecoCep',
            '@pagamentoStatus',
            '@pagamentoForma',
            '@pedidoID',
            '@pedidoTotalProd',
            '@pedidoTotalFrete',
            '@pedidoStatus',
            '@pedidoData',
            '@pedidoObservacoes',
            '@envioTransportadora',
            '@envioRastreamento',
            '@envioData',
            '@entregaData',
            '@origem',
            '@conta',
            '@sku',

            '@tracking_tipo',
            '@tracking_local',
            '@tracking_dataHora',
            '@tracking_descricao',
            '@tracking_detalhe',
        ];

        $message = str_replace($search, $replace, $Company->tracking_message);

        $message = str_replace($search, "não recebido", $message);

        return $message;
    }
}
