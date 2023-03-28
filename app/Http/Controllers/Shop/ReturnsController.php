<?php

namespace App\Http\Controllers\Shop;

use App\Models\ReturnMessages;
use App\Models\Returns;
use App\Models\SupplierOrders;
use App\Services\ReturnsService;
use Illuminate\Http\Request;
use Auth;

class ReturnsController extends Controller
{
    public function index(Request $request){
        $status = $request->status ? $request->status : 'pending';
        $shop_id = auth()->guard('shop')->id();


        $returns = Returns::whereHas('supplier_order', function($q) use ($shop_id){
            $q->leftJoin('orders', 'supplier_orders.order_id', '=', 'orders.id')->where('orders.shop_id', $shop_id);
        })->where('status', $status)->get();

        return view('shop.returns.index', compact('returns'));
    }

    public function confirm($id){
        $shop_id = auth()->guard('shop')->id();

        $result = ReturnsService::confirmByShop($id, $shop_id);

        return redirect()->back()->with(($result->success) ? 'success' : 'error', $result->message);
    }

    public function cancel($id){
        $shop_id = auth()->guard('shop')->id();

        $result = ReturnsService::cancel($id, $shop_id);

        return redirect()->back()->with(($result->success) ? 'success' : 'error', $result->message);
    }


    public function ask_return($sup_order_id){
        $shop = Auth::guard('shop')->user();
        $sup_order = SupplierOrders::whereHas('order', function($q) use ($shop){
            $q->where('shop_id', $shop->id);
        })->find($sup_order_id);

        if($sup_order){
            $return = Returns::with('messages')->where('supplier_order_id', $sup_order->id)->first();

            if(!$return){
                $return = new Returns();
                $return->supplier_order_id = $sup_order->id;
                $return->save();
            }

            if($sup_order){
                $return = $return->fresh();

                // Read pending messages
                ReturnMessages::where('return_id', $return->id)->whereNull('shop_id')->update(['read' => 1]);

                return view('shop.orders.return', compact('sup_order', 'return'));
            }else{
                return redirect()->back()->with(['error' => 'Pedido n達o encontrado.']);
            }
        }else{
            return redirect()->back()->with(['error' => 'Pedido n達o encontrado.']);
        }
    }

    public function returnNewMessage(Request $request){
        $shop = Auth::guard('shop')->user();
        $sup_order = SupplierOrders::whereHas('order', function($q) use ($shop){
            $q->where('shop_id', $shop->id);
        })->find($request->supplier_order_id);

        if($sup_order){
            $return = Returns::where('supplier_order_id', $sup_order->id)->first();

            if($return){
                $message = new ReturnMessages();
                $message->return_id = $return->id;
                $message->shop_id = $shop->id;
                $message->message = $request->message;
                $message->save();

                return redirect()->back()->with(['success' => 'Mensagem enviada com sucesso.']);
            }else{
                return redirect()->back()->with(['error' => 'Pedido n達o encontrado.']);
            }
        }else{
            return redirect()->back()->with(['error' => 'Pedido n達o encontrado.']);
        }
    }
    
    public function returnNewMessageimg(Request $request, $order_id){
        $shop = Auth::guard('shop')->user();
       
        $return = Returns::where('id', $order_id)->first(); 
            $return =  new Returns();
            $salvar = $return->where('id', $order_id)->first();
            $resp = Returns::find($salvar->id);
            
           
          
            if($request->hasFile('img_produto')){  
            $public =  'public/';
            //unlink anterior
            if(file_exists($public.'assets/imgdevprod/'.$resp->img_produto) && $resp->img_produto != '')
                unlink($public.'assets/imgdevprodv/'.$resp->img_produto);

            $file = $request->file('img_produto');

            $extensao = $file->getClientOriginalExtension();
            $nomeImagem = date('YmdHi').$file->getClientOriginalName() ;

            $upload = $file->move(public_path('assets/imgdevprod/'), $nomeImagem);
            $resp->img_produto = $nomeImagem;

            $resp->save();
            }
            if($request->hasFile('img_produto1')){  
                $public =  'public/';
                //unlink anterior
                if(file_exists($public.'assets/imgdevprod/'.$resp->img_produto) && $resp->img_produto != '')
                    unlink($public.'assets/imgdevprodv/'.$resp->img_produto);
    
                $file = $request->file('img_produto1');
    
                $extensao = $file->getClientOriginalExtension();
                $nomeImagem = date('YmdHi').$file->getClientOriginalName() ;
    
                $upload = $file->move(public_path('assets/imgdevprod/'), $nomeImagem);
                $resp->img_produto1 = $nomeImagem;
    
                $resp->save();

                if($request->hasFile('img_produto2')){  
                    $public =  'public/';
                    //unlink anterior
                    if(file_exists($public.'assets/imgdevprod/'.$resp->img_produto) && $resp->img_produto != '')
                        unlink($public.'assets/imgdevprodv/'.$resp->img_produto);
        
                    $file = $request->file('img_produto2');
        
                    $extensao = $file->getClientOriginalExtension();
                    $nomeImagem = date('YmdHi').$file->getClientOriginalName() ;
        
                    $upload = $file->move(public_path('assets/imgdevprod/'), $nomeImagem);
                    $resp->img_produto2 = $nomeImagem;
        
                    $resp->save();
                }    
                    if($request->hasFile('img_produto3')){  
                        $public =  'public/';
                        //unlink anterior
                        if(file_exists($public.'assets/imgdevprod/'.$resp->img_produto) && $resp->img_produto != '')
                            unlink($public.'assets/imgdevprodv/'.$resp->img_produto);
            
                        $file = $request->file('img_produto3');
            
                        $extensao = $file->getClientOriginalExtension();
                        $nomeImagem = date('YmdHi').$file->getClientOriginalName() ;
            
                        $upload = $file->move(public_path('assets/imgdevprod/'), $nomeImagem);
                        $resp->img_produto3 = $nomeImagem;
            
                        $resp->save();
                    }    
                        if($request->hasFile('img_produto4')){  
                            $public =  'public/';
                            //unlink anterior
                            if(file_exists($public.'assets/imgdevprod/'.$resp->img_produto) && $resp->img_produto != '')
                                unlink($public.'assets/imgdevprodv/'.$resp->img_produto);
                
                            $file = $request->file('img_produto4');
                
                            $extensao = $file->getClientOriginalExtension();
                            $nomeImagem = date('YmdHi').$file->getClientOriginalName() ;
                
                            $upload = $file->move(public_path('assets/imgdevprod/'), $nomeImagem);
                            $resp->img_produto4 = $nomeImagem;
                
                            $resp->save();
                        }   
                        
            return redirect()->back()->with(['success' => 'Imagem enviada com sucesso.']);
        }else{
            return redirect()->back()->with(['error' => 'Imagem não encontrada.']);
        
    }
        
}

    private function salveArquivo($request, $resp){
        if($request->hasFile('img_produto')){

            $public =  'public/';
            //unlink anterior
            if(file_exists($public.'assets/imgdev/'.$resp->img_produto) && $resp->img_produto != '')
                unlink($public.'assets/imgdev/'.$resp->img_produto);

            $file = $request->file('arquivo');

            $extensao = $file->getClientOriginalExtension();
            $nomeImagem = $file->getClientOriginalName();

            $upload = $file->move(public_path('assets/imgdev/'), $nomeImagem);
            $resp->img_produto = $nomeImagem;

            $resp->save();
        }else{

        }
    }
    
    
    
    
    
}
