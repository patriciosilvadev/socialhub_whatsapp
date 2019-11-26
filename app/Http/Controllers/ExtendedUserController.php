<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\ExtendedUserRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Auth;

class ExtendedUserController extends UserController
{

    public function __construct(ExtendedUserRepository $userRepository)
    {
        parent::__construct($userRepository);

        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userRepository->pushCriteria(new RequestCriteria($request));
        $users = $this->userRepository->all();

        return view('users.index')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();

        $User = Auth::check()? Auth::user():session('logged_user');
        $input['company_id'] = $User->company_id;
        $input['role_id'] = ExtendedContactsStatusController::ATTENDANT;

        // $this->withoutEvents();
        // $User->withoutEvents();

        $user = $this->userRepository->create($input);

        Flash::success('User saved successfully.');

        //TODO-Alberto: retornar el usuario creado para coger el id y poder insertar un atendente
        // return redirect(route('users.index'));
        return $user->toJson();
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified User in storage.
     *
     * @param  int              $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            
            return redirect(route('users.index'));
        }
        
        $user = $this->userRepository->update($request->all(), $id);
        
        Flash::success('User updated successfully.');

        return $user->toJson();
        //return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        $this->userRepository->delete($id);

        Flash::success('User deleted successfully.');

        // return redirect(route('users.index'));
    }

    public function update_image($id, Request $request)
    {
        //TODO: ALberto aydua aqui

        // try {
        //     $User = User::find($id);

        //     if ($file = $request->file('file')) {
        //         $image_path = "user_files/profile_images/";
        //         $image_name = "$id." . $file->getClientOriginalExtension();
        //         $json_data = App(ContentController::class)->upload($request, $image_path, $image_name);
        //         if ($json_data) {
        //             $User->image_path = $image_path . $image_name;
        //             $User->save();

        //             return $image_path . $image_name;
        //         }
        //     } else {
        //         abort(302, "Error uploading file!");
        //     }
        // } catch (\Throwable $th) {
        //     throw $th;
        // }
    }

    static function withoutEvents() {

    }
}
