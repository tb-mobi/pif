@if($status == '0')
    Не активен
@elseif($status == '1')
    Открыт
@elseif($status == '2')
    Открыт только на пополнение
@elseif($status == '3')
    Основной
@elseif($status == '4')
    Основной только на пополнение
@elseif($status == '5')
    Заблокирован
@elseif($status == '9')
    Закрыт
@endif
