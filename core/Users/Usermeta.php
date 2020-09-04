<?php 
namespace UltimateMember\Users;

class Usermeta extends \Illuminate\Database\Eloquent\Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usermeta';

    protected $primaryKey = 'umeta_id';

    protected $localKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable  = ['umeta_id','user_id','meta_key','meta_value'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    function __construct( array $attributes = [] ){
    
        parent::__construct( $attributes );
        
    
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'meta_key' => '',
        'meta_value' => '',
        
    ];

    /**
     * Get the user of the metas
     */
    public function user()
    {
        return $this->belongsTo('UltimateMember\Users\User')->withDefault();
    }
}