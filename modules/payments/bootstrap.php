<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package modules/payments
 */
class Module_Bootstrap_Payments extends Art_Abstract_Module_Bootstrap {
	
	static function initialize()
	{
		Art_Event::on(Art_Event::LABEL_SETUP,function(){
			static::labelSetup();
		});
	}
	
	static function labelSetup() 
	{
		Art_Label::addLabelSet(array(
			'module_payments_header'			=> 'Platby',
			'module_payments_user'				=> 'Uživatel',
			'module_payments_payer'				=> 'Plátce',
			'module_payments_service'			=> 'Služba',
			'module_payments_value'				=> 'Hodnota',
			'module_payments_price_per_time_interval'		=> 'Cena za časový úsek',
			'module_payments_new_header'		=> 'Nová platba',
			'module_payments_new_success'		=> 'Platba úspěšně zapsána',
			'module_payments_delete_success'	=> 'Platba byla úspěšně vymazána',
			'module_payments_delete_fail'		=> 'Platba nemohla být vymazána',
			'module_payments_date'				=> 'Datum',
			'module_payments_new_membership_header'		=> 'Nová platba za členství',
			
			'module_payments_v_value_min'			=> 'Hodnota je příliš nízká',
			'module_payments_v_value_not_integer'	=> 'Hodnota muí být číslo',
			'module_payments_v_id_user_not_integer'	=> 'Uživatel není platný',
			'module_payments_v_id_user_paid_by_not_integer'	=> 'Uživatel, který platil, není platný',
			'module_payments_v_id_user_group_x_service_price_not_integer'	=> 'Služba není platná',
			
			'module_payments_delete_single_confirm'	=> 'Opravdu chcete smazat tuto platbu?',
			));
	}
}