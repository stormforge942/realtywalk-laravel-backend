<?php

namespace App\Observers;

use App\Models\PropertyFavorite;
use App\Notifications\PropertyUpdate;
use Illuminate\Support\Facades\Notification;
use App\Models\Property;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyObserver
{
    public function saved(Property $property)
    {
        $property->generateSEOURL();
    }

    public function saving(Property $property)
    {
        $original_full_address_unabbreviated = $property->getOriginal('full_address_unabbrv');
        $full_address_unabbreviated = $property->getUnabbreviatedAddress();

        if ($original_full_address_unabbreviated !== $property->full_address_unabbrv && $full_address_unabbreviated) {
            $property->full_address_unabbrv = $full_address_unabbreviated;
        }
    }

    public function updating(Property $property)
    {
        $$excludeFields = ['created_at', 'updated_at', 'id'];
        $oldPropertyData = Arr::except($property->getOriginal(), $excludeFields);
        $newPropertyData = Arr::except($property->toArray(), $excludeFields);

        $oldPropertyData['image_urls'] = $oldPropertyData['image_urls'] ?? [];
        $newPropertyData['image_urls'] = $newPropertyData['image_urls'] ?? [];
        $oldPropertyData['image_urls'] = json_encode($oldPropertyData['image_urls']);
        $newPropertyData['image_urls'] = json_encode($newPropertyData['image_urls']);

        // flatten array
        $current = Arr::dot($oldPropertyData);
        $original = Arr::dot($newPropertyData);
        // check for differences
        $diffs = array_diff($current, $original);

		if(count($diffs) > 0){
			# Insert into history table
			DB::table('property_history')->insert([
				'property_id' => $property->id,
				'property_version_id' => Str::random(10), # Doesn't need to be unique across the whole dataset
				'property_version_timestamp' => $property->updated_at,
				'attributes_changed' => json_encode(array_keys($diffs)),
				'attributes_json'	=> json_encode($diffs),
				'created_at'	=> Carbon::now()->toDateTimeString(),
				'updated_at'	=> Carbon::now()->toDateTimeString()
			]);
		}

        $pricePercentageChange = $this->calculatePricePercentChange($property->price_from, $oldPropertyData['price_from']);

        $this->propertyUpdateNotify($oldPropertyData, $property, $pricePercentageChange);
    }

    private function propertyUpdateNotify($oldPropertyData, $property, $pricePercentageChange)
    {
        if ($oldPropertyData['price_from'] !== $property->price_from || $oldPropertyData['descr'] !== $property->descr) {
            $user_favorite_id = PropertyFavorite::where('property_id', $property->id)->pluck('user_id');
            $users = User::whereIn('id', $user_favorite_id)->get();
            $unitNumber = $property->unit_number ? " Unit {$property->unit_number}" : '';
            $propertyName = $property->title . ' ' . $property->full_address;
            $mailMessage = $this->composePropertyMailMsg($property, $pricePercentageChange);

            $mailData = [
                'subject' => 'Property Update - ' . $propertyName,
                'message' => $mailMessage,
                'url' => 'property/' . $property->id
            ];

            Notification::send($users, new PropertyUpdate($mailData));
        }
    }

    public function composePropertyMailMsg($property, $pricePercentageChange)
    {
        $pricePercentageChange = $pricePercentageChange < 0 ? $pricePercentageChange : '+' . $pricePercentageChange;

        $unitNumber = $property->unit_number ? " Unit {$property->unit_number}" : '';
        $message =  '<p>One of your favorited listing was updated.</p>' .
            '<h5>Title: </h5>' .
            $property->title .
            '<h5>Location: </h5>' .
            $property->full_address .
            '<h5>Description</h5>' .
            $property->descr .
            '<h5>Market Status</h5>' .
            $property->status .
            '<h5>Gallery Count</h5>' .
            $property->media()->count() .
            '<h5>MLS Number</h5>' .
            $property->mls_number .
            '<h5>Price</h5>' .
            number_format($property->price_from, 2) . ' ' . ($pricePercentageChange !== 0.0 ? '(' . $pricePercentageChange . '%)' : '');

        return $message;
    }

    private function calculatePricePercentChange($newPrice, $oldPrice)
    {
        $oldPrice = !$oldPrice ? 0 : $oldPrice;
        $newPrice = !$newPrice ? 0 : $newPrice;
        if ($oldPrice && $newPrice) {
            $percentChange = round((($newPrice - $oldPrice) / ($oldPrice)) * 100, 2);
        } else {
            $percentChange = 0.0;
        }
        return $percentChange;
    }
}
