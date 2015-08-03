<div class="content-block accounts">
    <div class='title'>Мои счета:</div>
    <table>
        <tbody>
            @foreach ($accounts as $account)
                @include('product.account',['account'=>$account])
            @endforeach
        </tbody>
    </table>
</div>
