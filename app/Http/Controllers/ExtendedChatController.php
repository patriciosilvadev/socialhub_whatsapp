<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChatRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Models\Contact;
use App\Repositories\ExtendedChatRepository;
use Auth;
use Flash;
use Illuminate\Http\Request;
use Response;

class ExtendedChatController extends ChatController
{
    private $APP_WP_API_URL;

    public function __construct(ExtendedChatRepository $chatRepo)
    {
        // $this->middleware('guest');

        $this->chatRepository = $chatRepo;

        $this->APP_WP_API_URL = env('APP_WP_API_URL');
    }

    /**
     * Display a listing of the Chat.
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $contact_id = (int) $request['contact_id'];
        $page = (int) $request['page'];
        $searchMessageByStringInput = (isset($request['searchMessageByStringInput'])) ? $request['searchMessageByStringInput'] : '';
        $chats = $this->chatRepository->contactChat($User->id, $contact_id, $page, $searchMessageByStringInput);
        return $chats->toJson();
    }

    /**
     * Store a newly created Chat in storage.
     * @param CreateChatRequest $request
     * @return Response
     */
    public function store(CreateChatRequest $request)
    {
        // Send text message to SH Rest API
        $User = Auth::check() ? Auth::user() : session('logged_user');
        $input = $request->all();
        $input['attendant_id'] = $User->id;

        $Contact = Contact::findOrFail($input['contact_id']);
        if ($this->send_message($input['message'], $Contact->whatsapp_id)) {
            $chat = $this->chatRepository->createMessage($input);
            return $chat->toJson();
        }

        return Flash::error('Chat saved successfully.');

        // Flash::success('Chat saved successfully.');
        // return redirect(route('chats.index'));
    }

    // /**
    //  * Store a newly created Chat in storage.
    //  * @param Request  $request
    //  * @return Response
    //  */
    // public function store(Request $request) {
    //     // Send text message to SH Rest API
    //     // $User = Auth::check()? Auth::user():session('logged_user');
    //     dd("ok");
    //     $input = $request->all();
    //     $contact_id = $input['Jid'];

    //     // $Contact = Contact::findOrFail($contact_id);
    //     $Contact = $this->with(['Status', 'latestAttendantContact'])->findWhere(['contact_id' => $contact_id])->get();
    //     dd($Contact);
    //     // $Contact = $this->with(['Status', 'latestAttendantContact'])->findWhere(['contact_id' => $contact_id])->each(function ($Contact, $key) {

    //     // });
    //     $Attendant = $Contact->latestAttendant();
    //     if ($this->send_message($input['message'], $Contact->whatsapp_id)) {
    //         $chat = $this->chatRepository->createMessage($input);
    //         return $chat->toJson();
    //     }

    //     return Flash::error('Chat saved successfully.');

    //     // Flash::success('Chat saved successfully.');
    //     // return redirect(route('chats.index'));
    // }

    public function send_message(string $message, string $contact_Jid)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $url = $this->APP_WP_API_URL . '/SendTextMessage';

            $form_params['RemoteJid'] = $contact_Jid;
            $form_params['Contact'] = Contact::where(['whatsapp_id' => $contact_Jid])->first();
            $form_params['Message'] = $message;
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'RemoteJid' => $contact_Jid,
                    'Message' => $message,
                ],
            ]);

            // dd($response);
            return $response;
        } catch (\Throwable $th) {
            throw $th;
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
        $chat = $this->chatRepository->findWithoutFail($id);

        if (empty($chat)) {
            Flash::error('Chat not found');

            return redirect(route('chats.index'));
        }

        $chat = $this->chatRepository->update($request->all(), $id);

        Flash::success('Chat updated successfully.');

        return redirect(route('chats.index'));
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

        return redirect(route('chats.index'));
    }
}
