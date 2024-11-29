<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listOrder(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with(['productImages', 'productDetails'])
                ->latest()
                ->get();
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('serial_number', function($row) {
                        static $index = 0;
                        return ++$index;
                    })
                    ->editColumn('thumbnail', function($row) {
                        if(!empty($row->productImages)){
                            $src = $row->productImages->thumbnail;
                            $image = '<img src="' . $src . '" alt="Profile Image" class="td_img_50">';
                        }else{
                            $image = '<img src="' . asset('admin/img/shop-img/no_product_img.png') . '" alt="Profile Image" class="td_img_50">';
                        }
                        return $image;
                    })
                    ->editColumn('category', function($row) {
                        if (!empty($row->productDetails->category)) {
                            return $row->productDetails->category->category_name;
                        } else {
                            return '#N/A';
                        }
                    })
                    ->editColumn('sub_category', function($row) {
                        if (!empty($row->productDetails->subCategory)) {
                            return $row->productDetails->subCategory->sub_category_name;
                        } else {
                            return '#N/A';
                        }
                    })
                    
                    ->editColumn('status', function($row) {
                        // 0 for Pending, 1 for Success, 2 for Rejected, 3 for Cancelled, 4 for Failed
                        if($row->status == 0 ){
                            return "Pending";
                        }else if($row->status == 1 ){
                            return 'Success';
                        }else if($row->status == 2 ){
                            return 'Rejected';
                        }else if($row->status == 3 ){
                            return 'Cancelled';
                        }else if($row->status == 2 ){
                            return 'Failed';
                        }
                    })
                    ->editColumn('total_price', function($row) {
                        if($row->total_price){
                            return $row->total_price;
                        }else{
                            return '#N/A';
                        }
                    })
                    ->editColumn('payment_method', function($row) {
                        if($row->payment_method){
                            return $row->payment_method;
                        }else{
                            return '#N/A';
                        }
                    })
                    // ->addColumn('action', function($row){
                    //     $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    //     return $btn;
                    // })
                    ->rawColumns(['product', 'thumbnail', 'action', 'category', 'sub_category', '', 'payment_method'])
                    ->make(true);
        }           
          
        return view('admin.order.list');
    }
}
