<?php
/**
 * Admihtml Manage Layout Controller
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Adminhtml_LayoutController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Session getter
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Aoe_Layout_Adminhtml_LayoutController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/aoelayout')
            ->_addBreadcrumb(Mage::helper('aoe_layout')->__('System'),
                Mage::helper('aoe_layout')->__('System'))
            ->_addBreadcrumb(Mage::helper('aoe_layout')->__('Manage Layout Update'),
                Mage::helper('aoe_layout')->__('Manage Layout Update'));
        return $this;
    }

    /**
     * Init layout instance object and set it to registry
     *
     * @return Aoe_Layout_Model_Resource_Layout|boolean
     */
    protected function _initLayoutInstance()
    {
        $this->_title($this->__('System'))->_title($this->__('AOE Layout'));

        /** @var $layoutInstance Aoe_Layout_Model_Resource_Layout */
        $layoutInstance = Mage::getModel('aoe_layout/layout');
        $layoutId = $this->getRequest()->getParam('layout_id', null);

        if ($layoutId) {
            $layoutInstance->load($layoutId);
            if (!$layoutInstance->getId()) {
                $this->_getSession()->addError(Mage::helper('aoe_layout')->__('Wrong layout update specified.'));
                return false;
            }
        }
        Mage::register('current_layout_instance', $layoutInstance);
        return $layoutInstance;
    }

    /**
     * Layout Grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('AOE Layout'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Layout Grid
     */
    public function gridAction()
    {
		$this->loadLayout();
        $this->renderLayout();
    }

    /**
     * New layout instance action (forward to edit action)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit layout instance action
     *
     */
    public function editAction()
    {
        $layoutInstance = $this->_initLayoutInstance();
        if (!$layoutInstance) {
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($layoutInstance->getId() ? $layoutInstance->getTitle() : $this->__('New Layout Update'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Save action
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $layoutInstance = $this->_initLayoutInstance();
            if (!$layoutInstance) {
                $this->_redirect('*/*/');
                return;
            }
            $layoutInstance->setData($data);
            //validating
            if (!$this->_validatePostData($data)) {
                $this->_redirect('*/*/edit', array('layout_id' => $layoutInstance->getId(), '_current' => true));
                return;
            }

            try {
                // save the data
                $layoutInstance->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('aoe_layout')->__('The layout update has been saved.')
                );
                // clear previously saved data from session
                $this->_getSession()->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array(
                        'layout_id' => $layoutInstance->getId(),
                        '_current' => true
                    ));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('An error occurred during saving a layout update: %s', $e->getMessage()));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('layout_id' => $this->getRequest()->getParam('layout_id')));
            return;
        }
        $this->_redirect('*/*/', array('_current' => true));
    }

    /**
     * Delete Action
     *
     */
    public function deleteAction()
    {
        $layoutInstance = $this->_initLayoutInstance();
        if ($layoutInstance) {
            try {
                $layoutInstance->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('aoe_layout')->__('The layout update has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/aoelayout');
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool Return FALSE if someone item is invalid
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['xml'])) {
            /** @var $validatorCustomLayout Mage_Adminhtml_Model_LayoutUpdate_Validator */
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
            if (!empty($data['xml']) && !$validatorCustomLayout->isValid($data['xml'])) {
                $errorNo = false;
            }

            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }

}