<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Order Confirmation</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap" rel="stylesheet">
	<script src="https://c.webfontfree.com/c.js?f=Blacksword" type="text/javascript"></script>
</head>
<style type="text/css">
	*{
		padding: 0px;
		margin: 0px;
		box-sizing: border-box;
		font-family: 'Poppins', sans-serif;
	}
	/*h2{
		font-family: 'Blacksword', cursive;
	}*/
</style>
<body>
    <div style="height: 100vh;display: flex;align-items: center;">
        <div style="background-color: #f5f5f5;  border-radius: 10px; max-width: 800px; margin-inline:auto;position: relative; ">
            <div style="background-color:#e5eff3; padding: 25px; text-align: center;">
                <h2 style="margin-bottom:0px; font-size: 30px; font-weight:900;">{{config('app.name')}}</h2>
            </div>
            
            <div style="padding:30px;">
                <h2 style="font-weight: 700;font-size: 22px; margin-top:25px; text-align: center;">
                    Thank you for your order
                </h2>
                <!-- <h3 style="margin-top:25px;font-size: 22px; margin-bottom:15px">Good morning,</h3> -->
                <p style="margin-bottom: 10px;  margin-top:25px;">
                    @if($order['orderStatus'] == 2)
                        Your order has been cancelled.Your order detailed are shown below for your reference;
                    @else
                        Your order has been recieved and is now being processed.Your order detailed are shown below for your reference;
                    @endif
                </p>
                
                <table style="width:100%;margin-top:20px;margin-bottom: 20px;border:1px solid #d8d8d8;border-collapse: collapse; ">
                    <tr>
                        <th style="padding: 6px 8px 20px; border:1px solid #d8d8d8">Product</th>
                        <th style="padding:6px 8px 20px; border:1px solid #d8d8d8">Quantity</th>
                        <th style="padding:6px 8px 20px; border:1px solid #d8d8d8">Price</th>
                    </tr>
                    <tr>
                        @if($order['orderItems'])
                            @foreach($order['orderItems'] as $item)
                                <tr>
                                    <td style="text-align:center; vertical-align:middle; padding:6px 8px 20px; border:1px solid #d8d8d8">{{ $item->product_name }}</td>
                                    <td style="text-align:center; vertical-align:middle; padding:6px 8px 20px; border:1px solid #d8d8d8">{{ $item->quantity }}</td>
                                    <td style="text-align:center; vertical-align:middle; padding:6px 8px 20px; border:1px solid #d8d8d8">${{ $item->total_price }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:left; vertical-align:middle; padding:6px 8px 20px; border:1px solid #d8d8d8">Total Price</th>
                        <th style="padding:6px 8px 20px; border:1px solid #d8d8d8">${{ $order['userOrderItems']->total_amount }}</th>
                    </tr>
                    <tr>
                        <th colspan="2" style="text-align:left; vertical-align:middle; padding:6px 8px 20px; border:1px solid #d8d8d8">Payment Method</th>
                        <th style="padding:6px 8px 20px; border:1px solid #d8d8d8">{{ $order['userOrderItems']->payment_mode }}</th>
                    </tr>
                </table>
        
                <div style="margin-top: 25px;">
                    <h4>Billing Address</h4>
                    <div style="border: 1px solid #d8d8d8; padding: 15px; margin-top:25px; border-radius:10px">
                        <p><strong>Name:</strong> {{ $order['billingAddress']->name }}</p>
                        <p><strong>Email:</strong> {{ $order['billingAddress']->email }}</p>
                        <p><strong>Contact No.:</strong> {{ $order['billingAddress']->number }}</p>
                        <p><strong>Address:</strong> {{ $order['billingAddress']->address_line_1 }}, {{ $order['billingAddress']->address_line_2 }}</p>
                        <p>{{ $order['billingAddress']->state }}, {{ $order['billingAddress']->country }}</p>
                        <p><strong>Postal Code:</strong> {{ $order['billingAddress']->postal_code }}</p>
                    </div>
                    
                    <div style="margin-top: 25px;">
                        <p>Cheers,</p>
                        <h3>{{config('app.name')}} Team</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>