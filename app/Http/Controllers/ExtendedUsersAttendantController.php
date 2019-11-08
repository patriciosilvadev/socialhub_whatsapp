<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUsersAttendantRequest;
use App\Http\Requests\UpdateUsersAttendantRequest;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Auth;

use App\Repositories\ExtendedUsersAttendantRepository;

class ExtendedUsersAttendantController extends UsersAttendantController
{
    public function __construct(ExtendedUsersAttendantRepository $usersAttendantRepo)
    {
        $this->usersAttendantRepository = $usersAttendantRepo;
    }

    /**
     * Display a listing of the UsersAttendant.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->usersAttendantRepository->pushCriteria(new RequestCriteria($request));
        //TODO: get manager_id form session
        $User = Auth::check()? Auth::user():session('logged_user');
        //TODO-ALBERTO: obtener el nombre del status del atendiente tambiem para mostrar al manager
        $usersAttendants = $this->usersAttendantRepository->Attendants_User((int)$User->id);
        
        return $usersAttendants->toJson();
    }

    /**
     * Update the specified UsersAttendant in storage.
     *
     * @param  int              $id
     * @param UpdateUsersAttendantRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUsersAttendantRequest $request)
    {
        $usersAttendant = $this->usersAttendantRepository->findWithoutFail($id);

        if (empty($usersAttendant)) {
            Flash::error('Users Attendant not found');

            return redirect(route('usersAttendants.index'));
        }
        $request->updated_at = time();
        $usersAttendant = $this->usersAttendantRepository->update($request->all(), $id);

        Flash::success('Users Attendant updated successfully.');

        return redirect(route('usersAttendants.index'));
    }

    /**
     * Store a newly created UsersAttendant in storage.
     *
     * @param CreateUsersAttendantRequest $request
     *
     * @return Response
     */
    public function store(CreateUsersAttendantRequest $request)
    {
        $usersAttendantController = new UsersAttendantController($this->usersAttendantRepository);
        $usersAttendantController->store($request);

        $input = $request->all();
        $this->usersAttendantRepository->createAttendantChatTable($input['user_id']);
    }

    /**
     * Remove the specified UsersAttendant from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {   
        $usersAttendant = $this->usersAttendantRepository->findWithoutFail($id);
        
        if (empty($usersAttendant)) {
            Flash::error('Users Attendant not found');
            
            return redirect(route('usersAttendants.index'));
        }
        
        $this->usersAttendantRepository->delete($id);
        
        Flash::success('Users Attendant deleted successfully.');
        
        // return redirect(route('usersAttendants.index'));
    }
}