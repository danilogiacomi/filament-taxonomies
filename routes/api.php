<?php

use Net7\FilamentTaxonomies\Http\Resources\EditorialMapResource;
use Net7\FilamentTaxonomies\Models\EditorialMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Net7\FilamentTaxonomies\Models\Document;
use Net7\FilamentTaxonomies\Models\Artwork;
use Net7\FilamentTaxonomies\Models\Layout;
use Net7\FilamentTaxonomies\Models\SectionModule;
use Backpack\NewsCRUD\app\Models\Article;
use Backpack\PageManager\app\Models\Page;
use Backpack\MenuCRUD\app\Models\MenuItem;
use Net7\FilamentTaxonomies\Http\Resources\DocumentResource;
use Net7\FilamentTaxonomies\Http\Resources\ArticleResource;
use Net7\FilamentTaxonomies\Http\Resources\ArtworkResource;
use Net7\FilamentTaxonomies\Http\Resources\ConceptResource;
use Net7\FilamentTaxonomies\Http\Resources\PageResource;
use Net7\FilamentTaxonomies\Http\Resources\MenuItemResourceCollection;
use Net7\FilamentTaxonomies\Http\Resources\LayoutResource;
use Net7\FilamentTaxonomies\Http\Resources\FooterResource;
use Net7\FilamentTaxonomies\Http\Resources\SourceResource;
use Net7\FilamentTaxonomies\Http\Resources\TranslationResource;
use Net7\FilamentTaxonomies\Models\Concept;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/

// Route::get('/menu', function () {
//     return new MenuItemResourceCollection(MenuItem::all());
// });

// Route::get('/footer', function () {
//     return new FooterResource(SectionModule::where('type', 'footer')->firstOrFail());
// });

// Route::get('/get_static/{slug}', function ($slug) {
//     return new PageResource(Page::where('slug', '=', $slug)->firstOrFail());
// });

// Route::get('/layout/{slug}', function ($slug) {
//     return new LayoutResource(Layout::where('name', '=', $slug)->firstOrFail());
// });

// Route::get('/translation/{lang}', function (Request $request, $lang) {
//     $request->merge(['lang' => $lang]);
//     return new TranslationResource(SectionModule::where('type', 'translation')->firstOrFail());
// });

/*
|--------------------------------------------------------------------------
| Concepts
|--------------------------------------------------------------------------
*/

Route::get('/concepts', function () {
    return ConceptResource::collection(Concept::all());
});

Route::get('/concept/{id}', function ($id) {
    return new ConceptResource(Concept::findOrFail($id));
});




Route::get('/taxonomy/{schema}', Net7\FilamentTaxonomies\Http\Controllers\Concepts::class)->name('filament-taxonomies-taxonomy');

/*
|--------------------------------------------------------------------------
| Editorial map
|--------------------------------------------------------------------------
// */

// Route::get('/editorial-map/{id}', function ($id) {
//     // Retrieve the EditorialMap with the specified relationships only if it has a status of 'published'
//     $editorialMap = EditorialMap::with(['periods', 'topics'])
//         ->where('status', 'published')
//         ->findOrFail($id);

//     // Return the EditorialMapResource
//     return new EditorialMapResource($editorialMap);
// });


/*
|--------------------------------------------------------------------------
| Articles
|--------------------------------------------------------------------------
*/

// Route::get('/articles', function () {
//     return ArticleResource::collection(Article::all());
// });

// Route::get('/article/{id}', function ($id) {
//     return new ArticleResource(Article::findOrFail($id));
// });

/*
|--------------------------------------------------------------------------
| Artworks
|--------------------------------------------------------------------------
*/

// Route::get('/artwork/{id}', function ($id) {
//     return new ArtworkResource(Artwork::findOrFail($id));
// });

/*
|--------------------------------------------------------------------------
| Sources
|--------------------------------------------------------------------------
*/

// Route::get('/source/{id}', function ($id) {
//     return new SourceResource(Source::findOrFail($id));
// });

