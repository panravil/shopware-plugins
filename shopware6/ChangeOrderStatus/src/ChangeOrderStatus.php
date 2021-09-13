<?php declare(strict_types=1);

namespace Change\OrderStatus;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class ChangeOrderStatus extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        $query = "
        CREATE TRIGGER `order_status_change` 
                    AFTER UPDATE 
                    ON `order_transaction` FOR EACH ROW
                    BEGIN 
                        SELECT sms.id INTO @order_progress_id
                        FROM state_machine sm
                        , state_machine_state sms
                        WHERE sm.technical_name = 'order.state'
                        AND 	sm.id = sms.state_machine_id
                        AND 	sms.technical_name = 'in_progress';
                    
                        SELECT sms.id INTO @order_cancel_id
                        FROM state_machine sm
                        , state_machine_state sms
                        WHERE sm.technical_name = 'order.state'
                        AND 	sm.id = sms.state_machine_id
                        AND 	sms.technical_name = 'cancelled';
                    
                        SELECT sms.id INTO @order_open_id
                        FROM state_machine sm
                        , state_machine_state sms
                        WHERE sm.technical_name = 'order.state'
                        AND 	sm.id = sms.state_machine_id
                        AND 	sms.technical_name = 'open';
                    
                        SELECT sms.id INTO @transaction_progress_id
                        FROM state_machine sm
                        , state_machine_state sms
                        WHERE sm.technical_name = 'order_transaction.state'
                        AND 	sm.id = sms.state_machine_id
                        AND 	sms.technical_name = 'in_progress';
                    
                        SELECT sms.id INTO @transaction_cancel_id
                        FROM state_machine sm
                        , state_machine_state sms
                        WHERE sm.technical_name = 'order_transaction.state'
                        AND 	sm.id = sms.state_machine_id
                        AND 	sms.technical_name = 'cancelled';
                        
                        SELECT sc.configuration_value INTO @change_order_status_configuration_value
                        FROM `system_config` sc 
                        where sc.configuration_key='ChangeOrderStatus.config.PaymentMethods' 
                        and sc.sales_channel_id IS NULL;

                        SELECT IF(JSON_SEARCH(@change_order_status_configuration_value,'one', LOWER(hex(new.payment_method_id))) IS NOT NULL, 1, 0) INTO @is_ignored_flag;
                    
                        IF(@is_ignored_flag = 0) THEN
                            IF (new.state_id = @transaction_progress_id) THEN
                                UPDATE `order`
                                SET state_id = @order_progress_id
                                WHERE id = NEW.order_id;
                            ELSEIF (new.state_id =  @transaction_cancel_id) THEN
                                UPDATE `order`
                                SET state_id = @order_cancel_id
                                WHERE id = NEW.order_id;
                            ELSE
                                UPDATE `order`
                                SET state_id = @order_open_id
                                WHERE id = NEW.order_id;
                            END IF;
                        END IF;
                    END;";

        $connection = $this->container->get(Connection::class);

        $connection->executeUpdate($query);

        parent::activate($activateContext);
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $connection = $this->container->get(Connection::class);
        $query = "DROP TRIGGER IF EXISTS order_status_change;";
        $connection->executeQuery($query);

        parent::deactivate($deactivateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            parent::uninstall($uninstallContext);

            return;
        }

        parent::uninstall($uninstallContext);
    }
}
