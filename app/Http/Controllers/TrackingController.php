<?php

namespace App\Http\Controllers;

use App\Business\MyResponse;
use App\Business\PostofficeBusiness;
use App\Http\Requests\CreateTrackingRequest;
use App\Http\Requests\UpdateTrackingRequest;
use App\Repositories\TrackingRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TrackingController extends AppBaseController
{

    const TRACKING_POSTED   = 1;
    const TRACKING_MOVING   = 2;
    const TRACKING_STOPPED  = 3;
    const TRACKING_RECEIVED = 4;
    const TRACKING_ARRIVED  = 5;

    const TRACKING_PROBLEM  = 7;

    /** @var  TrackingRepository */
    private $trackingRepository;

    public function __construct(TrackingRepository $trackingRepo)
    {
        $this->trackingRepository = $trackingRepo;
    }

    // public function create_company_tracking_jobs(Request $Request)
    // {
    //     try {
    //         // dd("ok  ");
            
    //         $Postoffice = new PostofficeBusiness();

    //         $Postoffice->createCompaniesJobs();

    //     } catch (\Throwable $th) {
    //         return MyResponse::makeExceptionJson($th);
    //     }

    //     return MyResponse::makeResponseJson("ok");
    // }

    public function trackings_import_csv(Request $Request)
    {
        try {
            $file = $Request->file;

            $Postoffice = new PostofficeBusiness();
            $response = $Postoffice->importCSV($file);

            return json_encode($response);

        } catch (\Throwable $th) {
            return MyResponse::makeExceptionJson($th);
        }

        return MyResponse::makeResponseJson("ok");
    }

    /**
     * Display a listing of the Tracking.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $User = Auth::check()? Auth::user():session('logged_user');
        
        $page = (int) ($request->page ?? '0');
        $searchInput = (string) ($request->searchInput ?? '');
        $filterStatus = ($request->filterStatus ?? 0);
        if(isset($request->betweenDates)){
            $betweenDates = json_decode($request->betweenDates);
            if($betweenDates[0] =="" || $betweenDates[1] =="" || $betweenDates[1]<$betweenDates[0])
                $betweenDates = null;
        }else 
            $betweenDates = null;
        if($betweenDates){
            $betweenDates[0] .= ' 00:00:01';
            $betweenDates[1] .= ' 23:59:59';
        }
        $trackings = $this->trackingRepository->trackingByCompany($User->company_id, $page, $searchInput, $filterStatus, $betweenDates);

        // dd($trackings[0]);

        return $trackings->toJson();

        // return view('trackings.index')
            // ->with('trackings', $trackings);
    }

    /**
     * Show the form for creating a new Tracking.
     *
     * @return Response
     */
    public function create()
    {
        return view('trackings.create');
    }

    /**
     * Store a newly created Tracking in storage.
     *
     * @param CreateTrackingRequest $request
     *
     * @return Response
     */
    public function store(CreateTrackingRequest $request)
    {
        $input = $request->all();

        $tracking = $this->trackingRepository->create($input);

        Flash::success('Tracking saved successfully.');

        return redirect(route('trackings.index'));
    }

    /**
     * Display the specified Tracking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tracking = $this->trackingRepository->findWithoutFail($id);

        if (empty($tracking)) {
            Flash::error('Tracking not found');

            return redirect(route('trackings.index'));
        }

        return view('trackings.show')->with('tracking', $tracking);
    }

    /**
     * Show the form for editing the specified Tracking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $tracking = $this->trackingRepository->findWithoutFail($id);

        if (empty($tracking)) {
            Flash::error('Tracking not found');

            return redirect(route('trackings.index'));
        }

        return view('trackings.edit')->with('tracking', $tracking);
    }

    /**
     * Update the specified Tracking in storage.
     *
     * @param  int              $id
     * @param UpdateTrackingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTrackingRequest $request)
    {
        $tracking = $this->trackingRepository->findWithoutFail($id);

        if (empty($tracking)) {
            Flash::error('Tracking not found');

            return redirect(route('trackings.index'));
        }

        $tracking = $this->trackingRepository->update($request->all(), $id);

        Flash::success('Tracking updated successfully.');

        return redirect(route('trackings.index'));
    }

    /**
     * Remove the specified Tracking from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        // $tracking = $this->trackingRepository->findWithoutFail($id);

        // if (empty($tracking)) {
        //     Flash::error('Tracking not found');

        //     return redirect(route('trackings.index'));
        // }

        // $this->trackingRepository->delete($id);

        // Flash::success('Tracking deleted successfully.');

        // return redirect(route('trackings.index'));

        try {
            $User = Auth::check()? Auth::user():session('logged_user');        
            $this->trackingRepository->deleteTracking($id, $User->company_id);
        } catch (\Throwable $th) {
            return 'false';
        }

        return 'true';

    }
}
