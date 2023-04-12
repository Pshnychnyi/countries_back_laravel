<?php

namespace App\Http\Controllers;
use App\Http\Resources\CountriesResource;
use App\Models\Country;
use App\Models\Continent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function countries(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'order' => 'required|integer',
            'continent' => 'nullable|string|exists:continents,code|max:3',
            'page' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $order = $data['order'] === 1 ? 'ASC': 'DESC';
        $continent = $data['continent'] ?? 'all';
        $page = $data['page'];

        $countries = $this->getCountries($page,  $continent, $order);
        return CountriesResource::collection($countries);

    }

    public function getCountries($page = 1, $continent = 'all', $order = 'ASC') {
        if($continent === 'all') {
            return Country::orderBy('name', $order)->paginate(10, ['*'], 'page', $page);
        }else {
            return Country::where('continent_code', $continent)->orderBy('name', $order)->paginate(10, ['*'], 'page', $page);
        }
    }

    public function country($id){
        return Country::where('country_id', $id)->get()->first();
    }

    public function addCountry(Request $request) {

        $data = $request->all();
        $validator = Validator::make($data, [
            'code' => 'required|string|unique:countries|max:3',
            'continent_code' => 'required|string|exists:continents,code|max:3',
            'display_order' => 'required|integer|max:999',
            'full_name' => 'required|string|max:128',
            'iso3' => 'required|string|max:3',
            'name' => 'required|string|max:64',
            'number' => 'required|integer|max:999'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => true, 'errors' => $validator->errors()]);
        }

        if(Country::create($data)) {
            return response()->json(['success' => true]);
        }




    }

    public function continents() {
        return Continent::all();
    }
}
