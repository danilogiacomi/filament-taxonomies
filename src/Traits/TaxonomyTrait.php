<?php

namespace Net7\FilamentTaxonomies\Traits;

trait TaxonomyTrait {
    
    // use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation { saveReorder as traitReorder; }

    /*
    * Creates the sortable string to be saved on database
    *
    * Database columns needed: id, parent_id
    *
    * @return Response 
    * @return string
    */
    public function createSortableString() {

        $parent_keys= "";
        if($this->parent_id != null){
            $parent = $this->parent;
            while ( $parent != null){
                $parent_keys = $parent_keys == "" ? $parent->id : $parent->id . "_" . $parent_keys;
                if($parent->parent_id) {
                    $parent = $parent->parent; 
                } else {
                    $parent = null;
                }
            }
            return $parent_keys . "_" . $this->id;
        } else 
        return $this->id;
    }
    
    public function getTreeLabelAttribute(){               
        $parent_keys= "";
        if($this->parent_id){
            $parent = $this->parent;
            while ( $parent != null){
                $parent_keys .= "—";
                if($parent->parent_id) {
                    $parent = $parent->parent; 
                } else {
                    $parent = null;
                }
            } 
        }
        return $parent_keys . " " . $this->getLabelForTree();        
    }
    
    private function getLabelForTree() {
        if($this->title){
            return $this->title;
        } else return $this[$this->getKeyName()];
    }
    
    public function getTreeBreadcrumb() {
        $parent_keys= "";
        if($this->parent_id){
            $parent = $this->parent;
            while ( $parent != null){
                $parent_keys .= " < " . $parent->getLabelForTree();
                if($parent->parent_id) {
                    $parent = $parent->parent; 
                } else {
                    $parent = null;
                }
            } 
        }
        return $parent_keys;      
    }
    
    
    
    /**
     * Save the new order, using the Nested Set pattern.
     * Save the sortable string based on new structure
     * Database columns needed: id, parent_id, lft, rgt, depth, name/title, sortable
     *
     * @return
     */
    public function saveReorder()
    {        
        // call the method in the trait
        $response = $this->traitReorder();
        $primaryKey = $this->crud->model->getKeyName();
        \DB::beginTransaction();        
        $items = $this->crud->model->all();
        foreach ($items as $item){            
            if($item->parent_id){
                $parent_keys= "";
                $parent = $item->parent;
                while ( $parent != null){
                    $parent_keys = $parent_keys == "" ? $parent->id : $parent->id . "_" . $parent_keys;
                    if($parent->parent_id) {
                        $parent = $parent->parent; 
                    } else {
                        $parent = null;
                    }
                }
                
                $this->crud->model->where($primaryKey, $item->id)->update([
                    'sortable' => $parent_keys . "_" .$item->id
                ]);
            } else {
                $this->crud->model->where($primaryKey, $item->id)->update([
                    'sortable' => $item->id
                ]);
            }
        }
        \DB::commit();
        return $response;
    }
    
    protected static function bootTaxonomyTrait(){
        
        static::created(function($model)
        {
            $model->setAttribute("sortable", $model->createSortableString());
            $model->save();
        });
        
        static::saved(function($model)
        {
            $model->setAttribute("sortable", $model->createSortableString());
            $model->saveQuietly();
        });
    }

}