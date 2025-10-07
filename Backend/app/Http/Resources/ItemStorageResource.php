<?php

namespace App\Http\Resources;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemStorageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $date_from = $request->input('date_from', '');
        $date_to = $request->input('date_to', '');

        $max_allow_day_flag = false;
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($date_from);
            $dateTo = Carbon::parse($date_to);
            $dayDiff = $dateFrom->diffInDays($dateTo);
            $max_allow_day_flag = $dayDiff <= $this->rented_max_allow_days;
        }

        $settings = Setting::first();

        $currency = config('constants.CURRENCY');

        $blocked_days = [
            'total_count' => count($this->itemStorageBlockDays),
            'dates' => $this->itemStorageBlockDays
        ];

        if($this->ratings->sum('rating') > 0){
            $ratingAVG = $this->ratings->sum('rating') / count($this->ratings);
        }

        return [
            'id' => $this->id,
            'listing_type' => $this->listing_type ?? '',
            'user_id' => $this->user_id ?? '',
            'map_pin' => $this->map_pin ?? '',
            'exception_details' => $this->exception_details ?? '',
            'description' => $this->description ?? '',
            'rate' => $this->rate ?? '',
            'default_storage_photo' => $this->default_storage_photo,
            'amount_in_text' => $currency . $this->rate . '/day' ?? '',
            'rented_max_allow_days' => $this->rented_max_allow_days ?? '',
            'location' => $this->location ?? '',
            'distance' => $this->distance ?? '',
            'country' => $this->country ?? '',
            'state' => $this->state ?? '',
            'city' => $this->city ?? '',
            'pincode' => $this->pincode ?? '',
            'landmark' => $this->landmark ?? '',
            'latitude' => $this->latitude ?? '',
            'longitude' => $this->longitude ?? '',
            'blocked_days' => $blocked_days,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'max_allow_day_flag' => $max_allow_day_flag,
            'category' => $this->category,
            'sub_category' => $this->subCategory,
            'seller' => $this->sellers,
            'offers' =>$this->facilityOffers,
            'termsConditions' => $this->termsConditions,
            'items' => $this->items,
            'photos' => $this->photos,
            'tags' => $this->tags,
            'rating_avg' => $ratingAVG ?? 0,
            'currency' => config('constants.CURRENCY') ?? '',
            'review' => $this->ratings->map(function ($rating) {
                return [
                    'reviewer_id' => $rating->seller_id,
                    'reviewer_name' => $rating->seller->name ?? '',
                    'avatar' => $rating->seller->avatar ?? '',
                    'review' => $rating->rating,
                    'title' => $rating->title ?? '',
                    'description' => $rating->description ?? '',
                    'day_ago' => $rating->created_at->diffForHumans() ?? ''
                ];
            }),
            'groups_created_by_seller' => $this->sellers->activeGroups,
            'groups_joined_by_seller' => $this->sellers->groupPivots,
            'application_fee' => $settings->application_fee ?? config('constants.APPLICATION_FEE'),
            'others_fee' => $settings->others_fee ?? config('constants.OTHER_FEE'),
            'tax' => $settings->tax ?? config('constants.TAX'),
        ];
    }
}
