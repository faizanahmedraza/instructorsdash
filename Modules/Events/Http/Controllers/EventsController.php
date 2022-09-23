<?php

namespace Modules\Events\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Events\Entities\Category;
use Modules\Events\Entities\Event;
use Modules\Events\Http\Requests\EventStoreRequest;
use Modules\Events\Http\Requests\EventUpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Nahid\JsonQ\Jsonq;
use Module;
use Modules\User\Entities\User;


class EventsController extends Controller
{
    public function index(Request $request)
    {
        $data = Event::query()->with(['category'])->withCount('guests');
        $data = $data->where('user_id', $request->user()->id);

        $query = $request->input('query', null);
        if (isset($query)) {
            $data->where('name', 'like', '%' . $query . '%');
        }
        $events = $data->paginate(config('events.per_page', 10));
        if (count($events) > 0) {
            foreach ($events as $event) {
                $event['available_seats'] = ($event->quantity == -1) ? 'un limited' : ($event->quantity - $event->guests_count);
            }
        }
        return view('events::events.index', [
            'events' => $events,
        ]);
    }

    public function store(EventStoreRequest $request)
    {
        $data = $request->validated();

        // default data
        $user = Auth::user();
        $data['short_slug'] = $user->id . time();
        $data['user_id'] = $user->id;
        $date_next = Carbon::now()->addDays(10);
        $data['register_end_date'] = $date_next;
        $data['start_date'] = $date_next;
        $data['end_date'] = Carbon::now()->addDays(15);
        $data['type'] = 'ONLINE';
        $data['quantity'] = -1;
        $data['tagline'] = 'tagline';
        $data['description'] = 'description';
        $data['noti_register_success'] = 'Registration successful!';
        $data['font_family'] = 'Open Sans';
        $data['info_items'] = [];
        $data['ticket_currency'] = 'USD';
        $data['ticket_items'] = [];


        $data['email_content'] = config('events.EVENT_EMAIL_CONTENT');
        $data['email_sender_name'] = $user->name;
        $data['email_sender_email'] = $user->email;
        $data['email_subject'] = 'Registration event successful!';


        $event = Event::create($data);

        return redirect()->route('events.edit', ['id' => $event->id])->with('success', __('Created success !'));
    }

    public function edit(Request $request, $id)
    {
        

        //dd(getConfigFileEventTemplate("lucky"));
        $event = Event::findOrFail($id);


        $allowCustomFonts = true;
        $allowCustomHeaderFooter = true;
        $allowCustomDomain = true;
        $allowPremiumTheme = true;
        if (Module::find('Saas')) {
            $user = User::findOrFail($event->user_id);
            $allowCustomFonts = $user->allowCustomFonts();
            $allowCustomHeaderFooter = $user->allowCustomHeaderFooter();
            $allowCustomDomain = $user->allowCustomDomain();
            $allowPremiumTheme = $user->allowPremiumTheme();
        }

        $categories = Category::get();
        $event_templates = getListEventTemplates();

        return view('events::events.edit', [
            'allowCustomHeaderFooter' => $allowCustomHeaderFooter,
            'allowCustomDomain' => $allowCustomDomain,
            'allowPremiumTheme' => $allowPremiumTheme,
            'allowCustomFonts' => $allowCustomFonts,
            'categories' => $categories,
            'event' => $event,
            'event_templates' => $event_templates
        ]);
    }

    public function update(EventUpdateRequest $request, $id)
    {
        $data = $request->validated();

        $event = Event::findOrfail($id);

        $data['quantity'] = intval($data['quantity']);
        $data['register_end_date'] = $data['register_end_date'];
        $data['start_date'] = $data['start_date'];

        if (!isset($data['info_items'])) $data['info_items'] = [];
        if (!isset($data['ticket_items'])) $data['ticket_items'] = [];

        if ($data['theme_color'] == '#000000') $data['theme_color'] == '#000001';//default color input
        isset($data['seo_enable']) ? $data['seo_enable'] = true : $data['seo_enable'] = false;

        // save landing page image
        $user_storage_path = 'users_storage/' . auth()->user()->id;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // delete image old
            $path = public_path('storage/') . $event->image;
            deleteImageWithPath($path);
            $image = $request->file('image')->store($user_storage_path, 'public');
            $data['image'] = $image;
        }

        // save image
        $user_storage_path = 'users_storage/' . auth()->user()->id;
        if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
            // delete image old
            $path = public_path('storage/') . $event->favicon;
            deleteImageWithPath($path);
            $favicon = $request->file('favicon')->store($user_storage_path, 'public');
            $data['favicon'] = $favicon;
        }

        if ($request->hasFile('social_image') && $request->file('social_image')->isValid()) {
            // delete image old
            $path = public_path('storage/') . $event->social_image;
            deleteImageWithPath($path);
            $social_image = $request->file('social_image')->store($user_storage_path, 'public');
            $data['social_image'] = $social_image;
        }

        if ($request->hasFile('background') && $request->file('background')->isValid()) {
            // delete image old
            $path = public_path('storage/') . $event->background;
            deleteImageWithPath($path);
            $background = $request->file('background')->store($user_storage_path, 'public');
            $data['background'] = $background;
        }

        $event->update($data);

        return redirect()->route('events.edit', ['id' => $id])->with('success', __('Updated success !'));
    }

    public function delete(Request $request, $id)
    {
        $item = Event::find($id);
        if ($item) {
            $item->delete();
            return redirect()->route('events.index')->with('success', __('Deleted Successfully'));
        }
        return redirect()->route('events.index')->with('error', __('Not found item'));
    }

    public function getFonts(Request $request)
    {
        $search_query = $request->search_query;
        $response = ['status' => true, 'data' => []];
        if ($search_query) {
            $jsonq = new Jsonq(base_path() . '/google-fonts.json');
            $result = $jsonq->from('items')->whereContains('family', $search_query)->get()->result();
            $response['status'] = true;
            $response['data'] = $result;
        }
        return response()->json($response);

    }
    
    public function copy(Request $request, $id)
    {
        $item = Event::find($id);
       
        if ($item) {
            $number=(int)$request->input('number') ?? 1;
            for($i=1 ; $i <= $number ; $i++){
                $newTask = $item->replicate();
                $newTask->name = $item->name." copy ".$i." ".date('Y-m-d H:i:s'); 
                $newTask->short_slug = \Auth::user()->id . time();
                $newTask->save();
            }
            return redirect()->route('events.index')->with('success', __('Copies of event created successfully.'));
        }
        return redirect()->back();
    }
    
     public function salesReview($id)
    {
        $item = Event::find($id);
       
        if ($item) {
            $guests=$item->guests;
             $data['count']=0;
             $data['price']=0;
            if(count($guests) > 0){
                foreach($guests as $guest){
                    if($guest->status=='registered'){
                        $data['count']+=1;
                        $price=$guest->ticket_price == null ? 0 : $guest->ticket_price;
                        $data['price']+=$price;
                    }
                }
            }
            
            return view('events::events.statistics', [
            'data' => $data,
        ]);
            
        }
        return redirect()->back();
    }
    
}
