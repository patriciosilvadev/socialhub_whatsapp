<?php

namespace App\Jobs;

use App\Events\MessageToAttendant;
use App\Http\Controllers\ExtendedChatController;
use App\Http\Controllers\ExternalRPIController;
use App\Http\Controllers\MessagesStatusController;
use App\Models\Contact;
use App\Models\Sales;
use App\Models\ExtendedChat;
use App\Models\MessagesStatus;
use App\Repositories\ExtendedChatRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMsgBling implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    
    
    /**
     * O --timeoutvalor sempre deve ser pelo menos alguns segundos menor que o retry_aftervalor da configuração. 
     *Isso garantirá que um trabalhador que processa um determinado trabalho seja sempre morto antes que o trabalho seja tentado novamente. 
     *Se sua --timeoutopção for maior que o retry_aftervalor de configuração, seus trabalhos poderão ser processados ​​duas vezes.     
     */
    public $timeout = 25;
    // Time worker will wait to retry again each work
    public $retryAfter = 30;
    
    // Time worker will be sleeping wheter not work
    public $sleep = 3;

    public $deleteWhenMissingModels = true;

    
    private $rpiController;

    private $Contact;

    private $objSale;
    private $objChat;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExternalRPIController $rpiController, Contact $Contact, $Chat, $Sale, string $queue = null)
    {
        $this->rpiController = $rpiController;

        $this->Contact = $Contact;
        $this->objSale = $Sale;
        $this->objChat = $Chat;

        $this->connection = 'database';

        $this->queue = $queue;

        // Time to wait before allow process Job
        $this->delay = 1;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug('Handle SendWhatsAppMsgBling...: ', [$this->objSale, $this->objChat]);
        $Sale = new Sales();
        $Sale->table = $this->Contact->company_id;
        $Sale = $Sale->find($this->objSale->id);

        $Chat = null;
        if ($this->objChat) {
            $Chat = new ExtendedChat();
            $Chat->table = $this->objChat->attendant_id;
            $Chat = $Chat->find($this->objChat->id);
        }
        
        $response = $this->rpiController->sendTextMessage($Sale->message, $this->Contact);
        // $response=null;
        Log::debug('\n\r SendingTextMessage to Contact contact_Jid from Job SendWhatsAppMsgBling handled: ', [$this->Contact->whatsapp_id]);
        
        $responseJson = json_decode($response);
        if (isset($responseJson->MsgID)) {
            $Sale->sended = true;
            if ($Chat) {
                $Chat->status_id = MessagesStatusController::SENDED;
                $Chat->save();
            }
        } else {
            $Sale->sended = false;
            if ($Chat) {
                $Chat->status_id = MessagesStatusController::FAIL;
                $Chat->save();
            }
        }
        
        $Sale->save();
    }
}