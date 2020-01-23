<?php

namespace App\Http\Controllers;

use App\Business\ChatsBusiness;
use App\Business\FileUtils;
use App\Events\NewContactMessage;
use App\Exceptions\MyHandler;
use App\Http\Requests\CreateChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\Chat;
use App\Models\Contact;
use App\Models\ExtendedChat;
use App\Models\UsersAttendant;
use App\Repositories\ExtendedChatRepository;
use Auth;
use Exception;
use Flash;
use function GuzzleHttp\json_encode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Response;

class ExtendedChatController extends ChatController
{
    public $externalRPiController = null;

    public function __construct(ExtendedChatRepository $chatRepo, ExternalRPIController $externalRPiController = null)
    {
        parent::__construct($chatRepo);

        $this->chatRepository = $chatRepo;
        $this->externalRPiController = $externalRPiController ?? new ExternalRPIController();
    }

    /**
     * Display a listing of the Chat.
     * @param Request $request
     * @return Response
     */
    public function getBagContact(Request $request)
    {
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $attendant_id = $User->id;

        $Contact = $this->chatRepository->getBagContact($attendant_id);

        $newContactsCount = (new ChatsBusiness())->getBagContactsCount($User->company_id);
        broadcast(new NewContactMessage($User->company_id, $newContactsCount));

        if ($Contact) {
            // Get cotact info (profile photo etc..)
            $Controller = new ExternalRPIController(null);
            $contactInfo = $Controller->getContactInfo($Contact->whatsapp_id);
            $Contact->json_data = $contactInfo;
            $contactInfo = json_decode($Contact->json_data);
            $Contact->first_name = ($contactInfo && strlen($contactInfo->name)) ? $contactInfo->name : $Contact->first_name;
            $Contact->picurl = ($contactInfo && strlen($contactInfo->picurl)) ? $contactInfo->picurl : "images/contacts/default.png";

            // Update contact without latestAttendant
            $UpdateContact = Contact::find($Contact->id);
            $UpdateContact->first_name = $Contact->first_name;
            $UpdateContact->json_data = $Contact->json_data ? $Contact->json_data : $UpdateContact->json_data;
            $UpdateContact->save();
        }

        return $Contact->toJson();
    }

    /**
     * Display a listing of the Chat.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) //

    {
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $contact_id = (int) $request['contact_id'];

        $page = (int) $request['page'];
        $searchMessageByStringInput = (isset($request['searchMessageByStringInput'])) ? $request['searchMessageByStringInput'] : '';

        $ContactChats = $this->chatRepository->contactChatAllAttendants($contact_id, $page, $searchMessageByStringInput);

        // Update selected_contact_id
        $userAttendant = UsersAttendant::find($User->id);
        $userAttendant->selected_contact_id = $contact_id;
        $userAttendant->save();

        return $ContactChats->toJson();
    }

    public function getBagContactsCount(Request $request) //

    {
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $newContactsCount = (new ChatsBusiness())->getBagContactsCount($User->company_id);

        return $newContactsCount;
    }

    /**
     * Display a listing of the Chat.
     * @param Request $request
     * @return Response
     */

    /**
     * Display a listing of the Chat.
     * @param Request $request
     * @return Response
     */
    public function indexOld(Request $request)
    {
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $contact_id = (int) $request['contact_id'];
        $page = (int) $request['page'];
        $searchMessageByStringInput = (isset($request['searchMessageByStringInput'])) ? $request['searchMessageByStringInput'] : '';

        $Contact = $this->chatRepository->contactChat($User->id, $contact_id, $page, $searchMessageByStringInput);

        return $Contact->toJson();
    }

    /**
     * Store a newly created Chat in storage.
     * @param CreateChatRequest $request
     * @return Response
     */
    public function store(CreateChatRequest $request)
    {
        try {
            // Send text message to SH Rest API
            $User = Auth::check() ? Auth::user() : session('logged_user');
            $input = $request->all();
            $input['attendant_id'] = $User->id;

            $Contact = Contact::findOrFail($input['contact_id']);
            $externalRPiController = new ExternalRPIController(null);

            $chat = $this->chatRepository->createMessage($input);

            if (isset($input['file'])) {
                $fileName = $chat->id; // Laravel Auto gerated file name
                // $envFilePath = env('APP_FILE_PATH');
                // $filePath = "$envFilePath/$Contact->company_id/contacts/$Contact->id/chat_files";
                $filePath = "companies/$Contact->company_id/contacts/$Contact->id/chat_files";
                $json_data = FileUtils::SavePostFile($request->file, $filePath, $fileName);
                if ($json_data) { // Save file to disk (public/app/..)
                    // $fileContent = Storage::disk('chats_files')->get("$json_data->FullPath"); // Retrive file like file_get_content(...)
                    $FileName = "$json_data->SavedFilePath/$json_data->SavedFileName";
                    // Convet From mp3 to ogg
                    if ($json_data->ClientOriginalExtension == 'mp3') {
                        $BaseDir = Storage::disk('chats_files')->getDriver()->getAdapter()->getPathPrefix();
                        $FileNameOgg = "$json_data->SavedFilePath/$json_data->SavedFileName.ogg";
                        $code = exec("ffmpeg -y -i $BaseDir/$FileName -acodec libvorbis $BaseDir/$FileNameOgg");
                        $FileName = $FileNameOgg;
                    }
                    $fileContent = Storage::disk('chats_files')->get($FileName); // Retrive file like file_get_content(...)
                    $response = $externalRPiController->sendFileMessage(
                        $fileContent, $json_data->SavedFileName, $input['type_id'],
                        $input['message'], $Contact
                    );

                    $chat->data = json_encode($json_data); 

                    $chat->save();
                    // $chat = $this->chatRepository->updateMessage($input, $chat->id);
                }
            } else {
                // $response = $externalRPiController->sendTextMessage($input['message'], $Contact);
                $response = $this->externalRPiController->sendTextMessage($input['message'], $Contact);
            }

            $responseJson = json_decode($response);
            if (isset($responseJson->MsgID)) {
                Flash::success('Chat saved successfully.');

                return $chat->toJson();
            } else {
                
                throw new Exception("Erro enviando mensagem, verifique conectividade!", 1);
            }
        } catch (\Throwable $th) {
            if (isset($chat->id)) {
                $ExtendedChat = new ExtendedChat();
                $ExtendedChat->table = $chat->attendant_id;
                $ExtendedChat = $ExtendedChat->find($chat->id);
                $ExtendedChat->delete($chat->id);
                Log::debug('Delete message', [$ExtendedChat]);
            }
            // return MyHandler::toJson($th, 500);
            return $th;
        }
    }

    /**
     * Update the specified Chat in storage.
     * @param  int              $id
     * @param UpdateChatRequest $request
     * @return Response
     */
    public function update($id, UpdateChatRequest $request)
    {
        $input = $request->all();
        $chat = $this->chatRepository->findWithoutFail($id);

        if (empty($chat)) {
            Flash::error('Chat not found');

            // return redirect(route('chats.index'));
            return null;
        }

        // $chat = $this->chatRepository->updateMessage($request->all(), $id);
        $chat = $this->chatRepository->update($request->all(), $id);

        Flash::success('Chat updated successfully.');

        // return redirect(route('chats.index'));

        return $chat->toJson();
    }

    /**
     * Remove the specified Chat from storage.
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $chat = $this->chatRepository->findWithoutFail($id);

        if (empty($chat)) {
            Flash::error('Chat not found');

            return redirect(route('chats.index'));
        }

        $this->chatRepository->delete($id);

        Flash::success('Chat deleted successfully.');

    }

}
