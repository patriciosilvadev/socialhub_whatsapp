<?php

namespace App\Models;

use App\Http\Controllers\StatusController;
use App\Http\Controllers\TrackingController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model as Model;
use stdClass;

/**
 * Class Tracking
 * @package App\Models
 * @version April 9, 2020, 6:00 pm -03
 *
 * @property string post_card
 * @property string|\Carbon\Carbon post_date
 * @property string service_code
 * @property integer items
 * @property string tracking_code
 * @property string json_csv_data
 * @property integer status_id
 * @property string tracking_list
 */
class Tracking extends Model
{

    public $table = 'tracking';

    public $incrementing = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $connection = "socialhub_mvp.tracking";

    public $fillable = [
        'id',
        'contact_id',
        'comentario',
        'post_card',
        'post_date',
        'service_code',
        'items',
        'tracking_code',
        'json_csv_data',
        'status_id',
        'tracking_list',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'contact_id' => 'string',
        'comentario' => 'string',
        'post_card' => 'string',
        'post_date' => 'datetime',
        'service_code' => 'string',
        'items' => 'integer',
        'tracking_code' => 'string',
        'json_csv_data' => 'string',
        'status_id' => 'integer',
        'tracking_list' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tracking_code' => 'required',
    ];

    /**
     * Class constructor.
     */
    public static function trackingConstruct(stdClass $objTracking, int $contact_id, int $company_id): Tracking
    {
        $Tracking = new Tracking();
        $Tracking->table = "$company_id";

        if ($objTracking->pedidoID) $Tracking->id = $objTracking->pedidoID;
        $Tracking->contact_id = $contact_id;
        $Tracking->post_date = $objTracking->pedidoData && strlen($objTracking->pedidoData) > 2 ? Carbon::createFromFormat('d/m/Y H:i', $objTracking->pedidoData)->toDateTimeString() : null;
        $Tracking->end_date = $objTracking->entregaData && strlen($objTracking->entregaData) > 2 ? Carbon::createFromFormat('d/m/Y H:i', $objTracking->entregaData)->toDateTimeString() : null;
        // $Tracking->post_date =  Carbon::createFromFormat('d/m/Y H:i', $objTracking->pedidoData)->toFormattedDateString('Y-m-d H:i:s');
        // $Tracking->post_date =  (new Carbon($objTracking->envioData))->toFormattedDateString('Y-m-d H:i:s');
        $Tracking->tracking_code = $objTracking->envioRastreamento;
        $Tracking->json_csv_data = json_encode($objTracking);
        $tracking_status = Status::find(1);
        $Tracking->status_id = $tracking_status->id;
        // $Tracking->status_id = TrackingController::TRACKING_POSTED;

        return $Tracking;
    }

    /**
     * Return last tracking event or null
     **/
    public function last_tracking()
    {
        $tracking_list = $this->tracking_list ? json_decode($this->tracking_list) : null;

        return is_array($tracking_list) ? $tracking_list[0] : null;
    }

    /**
     * 
     **/
    public function status_correios()
    {
        return $this->last_tracking();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     **/
    public function status()
    {
        return $this->morphOne(\App\Models\Status::class, 'statusable', 'statusable_type', 'statusable_id', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     **/
    public static function statuses()
    {
        // return $this->morphMany(\App\Models\Status::class, 'statusable', 'statusable_type', 'App\Models\Tracking');
        return Status::where(['statusable_type' => Tracking::class])->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function contact()
    {
        return $this->belongsTo(\App\Models\Contact::class, 'contact_id');
    }

}
