<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Events\NewTransferredContactEvent;
use App\Models\ExtendedChat;
use App\Models\User;
use App\Models\UsersAttendant;
use App\Repositories\ExtendedAttendantsContactRepository;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;

class ExtendedAttendantsContactController extends AttendantsContactController
{

    public function __construct(ExtendedAttendantsContactRepository $attendantsContactRepo)
    {
        parent::__construct($attendantsContactRepo);

        $this->attendantsContactRepository = $attendantsContactRepo;
    }

    public function deleteAllByContactId(int $contat_id)
    {
        $this->attendantsContactRepository->deleteAllByContactId($contat_id);
    }


    public function deleteAllByAttendantId(int $attendant_id, Request $request)
    {
        $this->attendantsContactRepository->deleteAllByAttendantId($attendant_id);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        
        $attendantsContact = $this->attendantsContactRepository->create($input);

        Flash::success('Attendants Contact saved successfully.');
        
        $User = Auth::check()? Auth::user():session('logged_user');
        $oldAttendant = User::find($User->id);

        $Contact = Contact::with('latestAttendant')->find($request->contact_id);
        $Contact->updated_at = time();
        $Contact->save();

        if(isset($request->transfering) || isset($input['transfering'])){
            //TODO-Alberto: enviar el last_message al igual que la funcion que me da los contactos
            $chatModel = new ExtendedChat();
            $chatModel->table = (string) $Contact->attendant_id;
            $lastMessage = $chatModel->where('contact_id', $Contact->id)->latest('created_at')->get()->first();
            $Contact->last_message = $lastMessage;
            broadcast(new NewTransferredContactEvent((int) $request->attendant_id, $Contact, $oldAttendant));
        }

        return $attendantsContact->toJson();
    }

}
