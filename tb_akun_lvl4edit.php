<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "tb_akun_lvl4info.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$tb_akun_lvl4_edit = NULL; // Initialize page object first

class ctb_akun_lvl4_edit extends ctb_akun_lvl4 {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}";

	// Table name
	var $TableName = 'tb_akun_lvl4';

	// Page object name
	var $PageObjName = 'tb_akun_lvl4_edit';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}
	var $AuditTrailOnAdd = FALSE;
	var $AuditTrailOnEdit = TRUE;
	var $AuditTrailOnDelete = FALSE;
	var $AuditTrailOnView = FALSE;
	var $AuditTrailOnViewData = FALSE;
	var $AuditTrailOnSearch = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (tb_akun_lvl4)
		if (!isset($GLOBALS["tb_akun_lvl4"]) || get_class($GLOBALS["tb_akun_lvl4"]) == "ctb_akun_lvl4") {
			$GLOBALS["tb_akun_lvl4"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tb_akun_lvl4"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tb_akun_lvl4', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("tb_akun_lvl4list.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->akun_lvl4_id->SetVisibility();
		$this->akun_lvl4_id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->akun_lvl1_id->SetVisibility();
		$this->akun_lvl2_id->SetVisibility();
		$this->akun_lvl3_id->SetVisibility();
		$this->akun_lvl4_nama->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $tb_akun_lvl4;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($tb_akun_lvl4);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();

			// Handle modal response
			if ($this->IsModal) {
				$row = array();
				$row["url"] = $url;
				echo ew_ArrayToJson(array($row));
			} else {
				header("Location: " . $url);
			}
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $IsModal = FALSE;
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		global $gbSkipHeaderFooter;

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;

		// Load key from QueryString
		if (@$_GET["akun_lvl4_id"] <> "") {
			$this->akun_lvl4_id->setQueryStringValue($_GET["akun_lvl4_id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->akun_lvl4_id->CurrentValue == "") {
			$this->Page_Terminate("tb_akun_lvl4list.php"); // Invalid key, return to list
		}

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("tb_akun_lvl4list.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				if (ew_GetPageName($sReturnUrl) == "tb_akun_lvl4list.php")
					$sReturnUrl = $this->AddMasterUrl($sReturnUrl); // List page, return to list page with correct master key if necessary
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->akun_lvl4_id->FldIsDetailKey)
			$this->akun_lvl4_id->setFormValue($objForm->GetValue("x_akun_lvl4_id"));
		if (!$this->akun_lvl1_id->FldIsDetailKey) {
			$this->akun_lvl1_id->setFormValue($objForm->GetValue("x_akun_lvl1_id"));
		}
		if (!$this->akun_lvl2_id->FldIsDetailKey) {
			$this->akun_lvl2_id->setFormValue($objForm->GetValue("x_akun_lvl2_id"));
		}
		if (!$this->akun_lvl3_id->FldIsDetailKey) {
			$this->akun_lvl3_id->setFormValue($objForm->GetValue("x_akun_lvl3_id"));
		}
		if (!$this->akun_lvl4_nama->FldIsDetailKey) {
			$this->akun_lvl4_nama->setFormValue($objForm->GetValue("x_akun_lvl4_nama"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->akun_lvl4_id->CurrentValue = $this->akun_lvl4_id->FormValue;
		$this->akun_lvl1_id->CurrentValue = $this->akun_lvl1_id->FormValue;
		$this->akun_lvl2_id->CurrentValue = $this->akun_lvl2_id->FormValue;
		$this->akun_lvl3_id->CurrentValue = $this->akun_lvl3_id->FormValue;
		$this->akun_lvl4_nama->CurrentValue = $this->akun_lvl4_nama->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->akun_lvl4_id->setDbValue($rs->fields('akun_lvl4_id'));
		$this->akun_lvl1_id->setDbValue($rs->fields('akun_lvl1_id'));
		if (array_key_exists('EV__akun_lvl1_id', $rs->fields)) {
			$this->akun_lvl1_id->VirtualValue = $rs->fields('EV__akun_lvl1_id'); // Set up virtual field value
		} else {
			$this->akun_lvl1_id->VirtualValue = ""; // Clear value
		}
		$this->akun_lvl2_id->setDbValue($rs->fields('akun_lvl2_id'));
		if (array_key_exists('EV__akun_lvl2_id', $rs->fields)) {
			$this->akun_lvl2_id->VirtualValue = $rs->fields('EV__akun_lvl2_id'); // Set up virtual field value
		} else {
			$this->akun_lvl2_id->VirtualValue = ""; // Clear value
		}
		$this->akun_lvl3_id->setDbValue($rs->fields('akun_lvl3_id'));
		if (array_key_exists('EV__akun_lvl3_id', $rs->fields)) {
			$this->akun_lvl3_id->VirtualValue = $rs->fields('EV__akun_lvl3_id'); // Set up virtual field value
		} else {
			$this->akun_lvl3_id->VirtualValue = ""; // Clear value
		}
		$this->akun_lvl4_nama->setDbValue($rs->fields('akun_lvl4_nama'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->akun_lvl4_id->DbValue = $row['akun_lvl4_id'];
		$this->akun_lvl1_id->DbValue = $row['akun_lvl1_id'];
		$this->akun_lvl2_id->DbValue = $row['akun_lvl2_id'];
		$this->akun_lvl3_id->DbValue = $row['akun_lvl3_id'];
		$this->akun_lvl4_nama->DbValue = $row['akun_lvl4_nama'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// akun_lvl4_id
		// akun_lvl1_id
		// akun_lvl2_id
		// akun_lvl3_id
		// akun_lvl4_nama

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// akun_lvl4_id
		$this->akun_lvl4_id->ViewValue = $this->akun_lvl4_id->CurrentValue;
		$this->akun_lvl4_id->ViewCustomAttributes = "";

		// akun_lvl1_id
		if ($this->akun_lvl1_id->VirtualValue <> "") {
			$this->akun_lvl1_id->ViewValue = $this->akun_lvl1_id->VirtualValue;
		} else {
			$this->akun_lvl1_id->ViewValue = $this->akun_lvl1_id->CurrentValue;
		if (strval($this->akun_lvl1_id->CurrentValue) <> "") {
			$sFilterWrk = "`akun_lvl1_id`" . ew_SearchString("=", $this->akun_lvl1_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `akun_lvl1_id`, `akun_lvl1_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl1`";
		$sWhereWrk = "";
		$this->akun_lvl1_id->LookupFilters = array("dx1" => "`akun_lvl1_nama`");
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->akun_lvl1_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->akun_lvl1_id->ViewValue = $this->akun_lvl1_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->akun_lvl1_id->ViewValue = $this->akun_lvl1_id->CurrentValue;
			}
		} else {
			$this->akun_lvl1_id->ViewValue = NULL;
		}
		}
		$this->akun_lvl1_id->ViewCustomAttributes = "";

		// akun_lvl2_id
		if ($this->akun_lvl2_id->VirtualValue <> "") {
			$this->akun_lvl2_id->ViewValue = $this->akun_lvl2_id->VirtualValue;
		} else {
			$this->akun_lvl2_id->ViewValue = $this->akun_lvl2_id->CurrentValue;
		if (strval($this->akun_lvl2_id->CurrentValue) <> "") {
			$sFilterWrk = "`akun_lvl2_id`" . ew_SearchString("=", $this->akun_lvl2_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `akun_lvl2_id`, `akun_lvl2_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl2`";
		$sWhereWrk = "";
		$this->akun_lvl2_id->LookupFilters = array("dx1" => "`akun_lvl2_nama`");
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->akun_lvl2_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->akun_lvl2_id->ViewValue = $this->akun_lvl2_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->akun_lvl2_id->ViewValue = $this->akun_lvl2_id->CurrentValue;
			}
		} else {
			$this->akun_lvl2_id->ViewValue = NULL;
		}
		}
		$this->akun_lvl2_id->ViewCustomAttributes = "";

		// akun_lvl3_id
		if ($this->akun_lvl3_id->VirtualValue <> "") {
			$this->akun_lvl3_id->ViewValue = $this->akun_lvl3_id->VirtualValue;
		} else {
			$this->akun_lvl3_id->ViewValue = $this->akun_lvl3_id->CurrentValue;
		if (strval($this->akun_lvl3_id->CurrentValue) <> "") {
			$sFilterWrk = "`akun_lvl3_id`" . ew_SearchString("=", $this->akun_lvl3_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `akun_lvl3_id`, `akun_lvl3_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl3`";
		$sWhereWrk = "";
		$this->akun_lvl3_id->LookupFilters = array("dx1" => "`akun_lvl3_nama`");
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->akun_lvl3_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->akun_lvl3_id->ViewValue = $this->akun_lvl3_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->akun_lvl3_id->ViewValue = $this->akun_lvl3_id->CurrentValue;
			}
		} else {
			$this->akun_lvl3_id->ViewValue = NULL;
		}
		}
		$this->akun_lvl3_id->ViewCustomAttributes = "";

		// akun_lvl4_nama
		$this->akun_lvl4_nama->ViewValue = $this->akun_lvl4_nama->CurrentValue;
		$this->akun_lvl4_nama->ViewCustomAttributes = "";

			// akun_lvl4_id
			$this->akun_lvl4_id->LinkCustomAttributes = "";
			$this->akun_lvl4_id->HrefValue = "";
			$this->akun_lvl4_id->TooltipValue = "";

			// akun_lvl1_id
			$this->akun_lvl1_id->LinkCustomAttributes = "";
			$this->akun_lvl1_id->HrefValue = "";
			$this->akun_lvl1_id->TooltipValue = "";

			// akun_lvl2_id
			$this->akun_lvl2_id->LinkCustomAttributes = "";
			$this->akun_lvl2_id->HrefValue = "";
			$this->akun_lvl2_id->TooltipValue = "";

			// akun_lvl3_id
			$this->akun_lvl3_id->LinkCustomAttributes = "";
			$this->akun_lvl3_id->HrefValue = "";
			$this->akun_lvl3_id->TooltipValue = "";

			// akun_lvl4_nama
			$this->akun_lvl4_nama->LinkCustomAttributes = "";
			$this->akun_lvl4_nama->HrefValue = "";
			$this->akun_lvl4_nama->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// akun_lvl4_id
			$this->akun_lvl4_id->EditAttrs["class"] = "form-control";
			$this->akun_lvl4_id->EditCustomAttributes = "";
			$this->akun_lvl4_id->EditValue = $this->akun_lvl4_id->CurrentValue;
			$this->akun_lvl4_id->ViewCustomAttributes = "";

			// akun_lvl1_id
			$this->akun_lvl1_id->EditAttrs["class"] = "form-control";
			$this->akun_lvl1_id->EditCustomAttributes = "";
			$this->akun_lvl1_id->EditValue = ew_HtmlEncode($this->akun_lvl1_id->CurrentValue);
			if (strval($this->akun_lvl1_id->CurrentValue) <> "") {
				$sFilterWrk = "`akun_lvl1_id`" . ew_SearchString("=", $this->akun_lvl1_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `akun_lvl1_id`, `akun_lvl1_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl1`";
			$sWhereWrk = "";
			$this->akun_lvl1_id->LookupFilters = array("dx1" => "`akun_lvl1_nama`");
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->akun_lvl1_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->akun_lvl1_id->EditValue = $this->akun_lvl1_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->akun_lvl1_id->EditValue = ew_HtmlEncode($this->akun_lvl1_id->CurrentValue);
				}
			} else {
				$this->akun_lvl1_id->EditValue = NULL;
			}
			$this->akun_lvl1_id->PlaceHolder = ew_RemoveHtml($this->akun_lvl1_id->FldCaption());

			// akun_lvl2_id
			$this->akun_lvl2_id->EditAttrs["class"] = "form-control";
			$this->akun_lvl2_id->EditCustomAttributes = "";
			$this->akun_lvl2_id->EditValue = ew_HtmlEncode($this->akun_lvl2_id->CurrentValue);
			if (strval($this->akun_lvl2_id->CurrentValue) <> "") {
				$sFilterWrk = "`akun_lvl2_id`" . ew_SearchString("=", $this->akun_lvl2_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `akun_lvl2_id`, `akun_lvl2_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl2`";
			$sWhereWrk = "";
			$this->akun_lvl2_id->LookupFilters = array("dx1" => "`akun_lvl2_nama`");
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->akun_lvl2_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->akun_lvl2_id->EditValue = $this->akun_lvl2_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->akun_lvl2_id->EditValue = ew_HtmlEncode($this->akun_lvl2_id->CurrentValue);
				}
			} else {
				$this->akun_lvl2_id->EditValue = NULL;
			}
			$this->akun_lvl2_id->PlaceHolder = ew_RemoveHtml($this->akun_lvl2_id->FldCaption());

			// akun_lvl3_id
			$this->akun_lvl3_id->EditAttrs["class"] = "form-control";
			$this->akun_lvl3_id->EditCustomAttributes = "";
			$this->akun_lvl3_id->EditValue = ew_HtmlEncode($this->akun_lvl3_id->CurrentValue);
			if (strval($this->akun_lvl3_id->CurrentValue) <> "") {
				$sFilterWrk = "`akun_lvl3_id`" . ew_SearchString("=", $this->akun_lvl3_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			$sSqlWrk = "SELECT `akun_lvl3_id`, `akun_lvl3_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl3`";
			$sWhereWrk = "";
			$this->akun_lvl3_id->LookupFilters = array("dx1" => "`akun_lvl3_nama`");
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->akun_lvl3_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = Conn()->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$arwrk = array();
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$this->akun_lvl3_id->EditValue = $this->akun_lvl3_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->akun_lvl3_id->EditValue = ew_HtmlEncode($this->akun_lvl3_id->CurrentValue);
				}
			} else {
				$this->akun_lvl3_id->EditValue = NULL;
			}
			$this->akun_lvl3_id->PlaceHolder = ew_RemoveHtml($this->akun_lvl3_id->FldCaption());

			// akun_lvl4_nama
			$this->akun_lvl4_nama->EditAttrs["class"] = "form-control";
			$this->akun_lvl4_nama->EditCustomAttributes = "";
			$this->akun_lvl4_nama->EditValue = ew_HtmlEncode($this->akun_lvl4_nama->CurrentValue);
			$this->akun_lvl4_nama->PlaceHolder = ew_RemoveHtml($this->akun_lvl4_nama->FldCaption());

			// Edit refer script
			// akun_lvl4_id

			$this->akun_lvl4_id->LinkCustomAttributes = "";
			$this->akun_lvl4_id->HrefValue = "";

			// akun_lvl1_id
			$this->akun_lvl1_id->LinkCustomAttributes = "";
			$this->akun_lvl1_id->HrefValue = "";

			// akun_lvl2_id
			$this->akun_lvl2_id->LinkCustomAttributes = "";
			$this->akun_lvl2_id->HrefValue = "";

			// akun_lvl3_id
			$this->akun_lvl3_id->LinkCustomAttributes = "";
			$this->akun_lvl3_id->HrefValue = "";

			// akun_lvl4_nama
			$this->akun_lvl4_nama->LinkCustomAttributes = "";
			$this->akun_lvl4_nama->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->akun_lvl1_id->FldIsDetailKey && !is_null($this->akun_lvl1_id->FormValue) && $this->akun_lvl1_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->akun_lvl1_id->FldCaption(), $this->akun_lvl1_id->ReqErrMsg));
		}
		if (!$this->akun_lvl2_id->FldIsDetailKey && !is_null($this->akun_lvl2_id->FormValue) && $this->akun_lvl2_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->akun_lvl2_id->FldCaption(), $this->akun_lvl2_id->ReqErrMsg));
		}
		if (!$this->akun_lvl3_id->FldIsDetailKey && !is_null($this->akun_lvl3_id->FormValue) && $this->akun_lvl3_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->akun_lvl3_id->FldCaption(), $this->akun_lvl3_id->ReqErrMsg));
		}
		if (!$this->akun_lvl4_nama->FldIsDetailKey && !is_null($this->akun_lvl4_nama->FormValue) && $this->akun_lvl4_nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->akun_lvl4_nama->FldCaption(), $this->akun_lvl4_nama->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// akun_lvl1_id
			$this->akun_lvl1_id->SetDbValueDef($rsnew, $this->akun_lvl1_id->CurrentValue, 0, $this->akun_lvl1_id->ReadOnly);

			// akun_lvl2_id
			$this->akun_lvl2_id->SetDbValueDef($rsnew, $this->akun_lvl2_id->CurrentValue, 0, $this->akun_lvl2_id->ReadOnly);

			// akun_lvl3_id
			$this->akun_lvl3_id->SetDbValueDef($rsnew, $this->akun_lvl3_id->CurrentValue, 0, $this->akun_lvl3_id->ReadOnly);

			// akun_lvl4_nama
			$this->akun_lvl4_nama->SetDbValueDef($rsnew, $this->akun_lvl4_nama->CurrentValue, "", $this->akun_lvl4_nama->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		if ($EditRow) {
			$this->WriteAuditTrailOnEdit($rsold, $rsnew);
		}
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("tb_akun_lvl4list.php"), "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_akun_lvl1_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl1_id` AS `LinkFld`, `akun_lvl1_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl1`";
			$sWhereWrk = "{filter}";
			$this->akun_lvl1_id->LookupFilters = array("dx1" => "`akun_lvl1_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => "`akun_lvl1_id` = {filter_value}", "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl1_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_akun_lvl2_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl2_id` AS `LinkFld`, `akun_lvl2_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl2`";
			$sWhereWrk = "{filter}";
			$this->akun_lvl2_id->LookupFilters = array("dx1" => "`akun_lvl2_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => "`akun_lvl2_id` = {filter_value}", "t0" => "3", "fn0" => "", "f1" => "`akun_lvl1_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl2_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_akun_lvl3_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl3_id` AS `LinkFld`, `akun_lvl3_nama` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tb_akun_lvl3`";
			$sWhereWrk = "{filter}";
			$this->akun_lvl3_id->LookupFilters = array("dx1" => "`akun_lvl3_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => "`akun_lvl3_id` = {filter_value}", "t0" => "3", "fn0" => "", "f1" => "`akun_lvl2_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl3_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_akun_lvl1_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl1_id`, `akun_lvl1_nama` AS `DispFld` FROM `tb_akun_lvl1`";
			$sWhereWrk = "`akun_lvl1_nama` LIKE '{query_value}%'";
			$this->akun_lvl1_id->LookupFilters = array("dx1" => "`akun_lvl1_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl1_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_akun_lvl2_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl2_id`, `akun_lvl2_nama` AS `DispFld` FROM `tb_akun_lvl2`";
			$sWhereWrk = "(`akun_lvl2_nama` LIKE '{query_value}%') AND ({filter})";
			$this->akun_lvl2_id->LookupFilters = array("dx1" => "`akun_lvl2_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f1" => "`akun_lvl1_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl2_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_akun_lvl3_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `akun_lvl3_id`, `akun_lvl3_nama` AS `DispFld` FROM `tb_akun_lvl3`";
			$sWhereWrk = "(`akun_lvl3_nama` LIKE '{query_value}%') AND ({filter})";
			$this->akun_lvl3_id->LookupFilters = array("dx1" => "`akun_lvl3_nama`");
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f1" => "`akun_lvl2_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->akun_lvl3_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tb_akun_lvl4';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		global $Language;
		if (!$this->AuditTrailOnEdit) return;
		$table = 'tb_akun_lvl4';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['akun_lvl4_id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$usr = CurrentUserName();
		foreach (array_keys($rsnew) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") { // Password Field
						$oldvalue = $Language->Phrase("PasswordMask");
						$newvalue = $Language->Phrase("PasswordMask");
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
			}
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tb_akun_lvl4_edit)) $tb_akun_lvl4_edit = new ctb_akun_lvl4_edit();

// Page init
$tb_akun_lvl4_edit->Page_Init();

// Page main
$tb_akun_lvl4_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tb_akun_lvl4_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = ftb_akun_lvl4edit = new ew_Form("ftb_akun_lvl4edit", "edit");

// Validate form
ftb_akun_lvl4edit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_akun_lvl1_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_akun_lvl4->akun_lvl1_id->FldCaption(), $tb_akun_lvl4->akun_lvl1_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_akun_lvl2_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_akun_lvl4->akun_lvl2_id->FldCaption(), $tb_akun_lvl4->akun_lvl2_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_akun_lvl3_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_akun_lvl4->akun_lvl3_id->FldCaption(), $tb_akun_lvl4->akun_lvl3_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_akun_lvl4_nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $tb_akun_lvl4->akun_lvl4_nama->FldCaption(), $tb_akun_lvl4->akun_lvl4_nama->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
ftb_akun_lvl4edit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftb_akun_lvl4edit.ValidateRequired = true;
<?php } else { ?>
ftb_akun_lvl4edit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftb_akun_lvl4edit.Lists["x_akun_lvl1_id"] = {"LinkField":"x_akun_lvl1_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_akun_lvl1_nama","","",""],"ParentFields":[],"ChildFields":["x_akun_lvl2_id"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"tb_akun_lvl1"};
ftb_akun_lvl4edit.Lists["x_akun_lvl2_id"] = {"LinkField":"x_akun_lvl2_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_akun_lvl2_nama","","",""],"ParentFields":["x_akun_lvl1_id"],"ChildFields":["x_akun_lvl3_id"],"FilterFields":["x_akun_lvl1_id"],"Options":[],"Template":"","LinkTable":"tb_akun_lvl2"};
ftb_akun_lvl4edit.Lists["x_akun_lvl3_id"] = {"LinkField":"x_akun_lvl3_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_akun_lvl3_nama","","",""],"ParentFields":["x_akun_lvl2_id"],"ChildFields":[],"FilterFields":["x_akun_lvl2_id"],"Options":[],"Template":"","LinkTable":"tb_akun_lvl3"};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$tb_akun_lvl4_edit->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $tb_akun_lvl4_edit->ShowPageHeader(); ?>
<?php
$tb_akun_lvl4_edit->ShowMessage();
?>
<form name="ftb_akun_lvl4edit" id="ftb_akun_lvl4edit" class="<?php echo $tb_akun_lvl4_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($tb_akun_lvl4_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $tb_akun_lvl4_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="tb_akun_lvl4">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($tb_akun_lvl4_edit->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<div>
<?php if ($tb_akun_lvl4->akun_lvl4_id->Visible) { // akun_lvl4_id ?>
	<div id="r_akun_lvl4_id" class="form-group">
		<label id="elh_tb_akun_lvl4_akun_lvl4_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_akun_lvl4->akun_lvl4_id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $tb_akun_lvl4->akun_lvl4_id->CellAttributes() ?>>
<span id="el_tb_akun_lvl4_akun_lvl4_id">
<span<?php echo $tb_akun_lvl4->akun_lvl4_id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $tb_akun_lvl4->akun_lvl4_id->EditValue ?></p></span>
</span>
<input type="hidden" data-table="tb_akun_lvl4" data-field="x_akun_lvl4_id" name="x_akun_lvl4_id" id="x_akun_lvl4_id" value="<?php echo ew_HtmlEncode($tb_akun_lvl4->akun_lvl4_id->CurrentValue) ?>">
<?php echo $tb_akun_lvl4->akun_lvl4_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_akun_lvl4->akun_lvl1_id->Visible) { // akun_lvl1_id ?>
	<div id="r_akun_lvl1_id" class="form-group">
		<label id="elh_tb_akun_lvl4_akun_lvl1_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_akun_lvl4->akun_lvl1_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_akun_lvl4->akun_lvl1_id->CellAttributes() ?>>
<span id="el_tb_akun_lvl4_akun_lvl1_id">
<?php $tb_akun_lvl4->akun_lvl1_id->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$tb_akun_lvl4->akun_lvl1_id->EditAttrs["onchange"]; ?>
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_akun_lvl1_id"><?php echo (strval($tb_akun_lvl4->akun_lvl1_id->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $tb_akun_lvl4->akun_lvl1_id->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($tb_akun_lvl4->akun_lvl1_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_akun_lvl1_id',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="tb_akun_lvl4" data-field="x_akun_lvl1_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $tb_akun_lvl4->akun_lvl1_id->DisplayValueSeparatorAttribute() ?>" name="x_akun_lvl1_id" id="x_akun_lvl1_id" value="<?php echo $tb_akun_lvl4->akun_lvl1_id->CurrentValue ?>"<?php echo $tb_akun_lvl4->akun_lvl1_id->EditAttributes() ?>>
<input type="hidden" name="s_x_akun_lvl1_id" id="s_x_akun_lvl1_id" value="<?php echo $tb_akun_lvl4->akun_lvl1_id->LookupFilterQuery() ?>">
</span>
<?php echo $tb_akun_lvl4->akun_lvl1_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_akun_lvl4->akun_lvl2_id->Visible) { // akun_lvl2_id ?>
	<div id="r_akun_lvl2_id" class="form-group">
		<label id="elh_tb_akun_lvl4_akun_lvl2_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_akun_lvl4->akun_lvl2_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_akun_lvl4->akun_lvl2_id->CellAttributes() ?>>
<span id="el_tb_akun_lvl4_akun_lvl2_id">
<?php $tb_akun_lvl4->akun_lvl2_id->EditAttrs["onchange"] = "ew_UpdateOpt.call(this); " . @$tb_akun_lvl4->akun_lvl2_id->EditAttrs["onchange"]; ?>
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_akun_lvl2_id"><?php echo (strval($tb_akun_lvl4->akun_lvl2_id->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $tb_akun_lvl4->akun_lvl2_id->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($tb_akun_lvl4->akun_lvl2_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_akun_lvl2_id',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="tb_akun_lvl4" data-field="x_akun_lvl2_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $tb_akun_lvl4->akun_lvl2_id->DisplayValueSeparatorAttribute() ?>" name="x_akun_lvl2_id" id="x_akun_lvl2_id" value="<?php echo $tb_akun_lvl4->akun_lvl2_id->CurrentValue ?>"<?php echo $tb_akun_lvl4->akun_lvl2_id->EditAttributes() ?>>
<input type="hidden" name="s_x_akun_lvl2_id" id="s_x_akun_lvl2_id" value="<?php echo $tb_akun_lvl4->akun_lvl2_id->LookupFilterQuery() ?>">
</span>
<?php echo $tb_akun_lvl4->akun_lvl2_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_akun_lvl4->akun_lvl3_id->Visible) { // akun_lvl3_id ?>
	<div id="r_akun_lvl3_id" class="form-group">
		<label id="elh_tb_akun_lvl4_akun_lvl3_id" class="col-sm-2 control-label ewLabel"><?php echo $tb_akun_lvl4->akun_lvl3_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_akun_lvl4->akun_lvl3_id->CellAttributes() ?>>
<span id="el_tb_akun_lvl4_akun_lvl3_id">
<span class="ewLookupList">
	<span onclick="jQuery(this).parent().next().click();" tabindex="-1" class="form-control ewLookupText" id="lu_x_akun_lvl3_id"><?php echo (strval($tb_akun_lvl4->akun_lvl3_id->ViewValue) == "" ? $Language->Phrase("PleaseSelect") : $tb_akun_lvl4->akun_lvl3_id->ViewValue); ?></span>
</span>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($tb_akun_lvl4->akun_lvl3_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x_akun_lvl3_id',m:0,n:10});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" data-table="tb_akun_lvl4" data-field="x_akun_lvl3_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $tb_akun_lvl4->akun_lvl3_id->DisplayValueSeparatorAttribute() ?>" name="x_akun_lvl3_id" id="x_akun_lvl3_id" value="<?php echo $tb_akun_lvl4->akun_lvl3_id->CurrentValue ?>"<?php echo $tb_akun_lvl4->akun_lvl3_id->EditAttributes() ?>>
<input type="hidden" name="s_x_akun_lvl3_id" id="s_x_akun_lvl3_id" value="<?php echo $tb_akun_lvl4->akun_lvl3_id->LookupFilterQuery() ?>">
</span>
<?php echo $tb_akun_lvl4->akun_lvl3_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($tb_akun_lvl4->akun_lvl4_nama->Visible) { // akun_lvl4_nama ?>
	<div id="r_akun_lvl4_nama" class="form-group">
		<label id="elh_tb_akun_lvl4_akun_lvl4_nama" for="x_akun_lvl4_nama" class="col-sm-2 control-label ewLabel"><?php echo $tb_akun_lvl4->akun_lvl4_nama->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $tb_akun_lvl4->akun_lvl4_nama->CellAttributes() ?>>
<span id="el_tb_akun_lvl4_akun_lvl4_nama">
<input type="text" data-table="tb_akun_lvl4" data-field="x_akun_lvl4_nama" name="x_akun_lvl4_nama" id="x_akun_lvl4_nama" size="30" maxlength="25" placeholder="<?php echo ew_HtmlEncode($tb_akun_lvl4->akun_lvl4_nama->getPlaceHolder()) ?>" value="<?php echo $tb_akun_lvl4->akun_lvl4_nama->EditValue ?>"<?php echo $tb_akun_lvl4->akun_lvl4_nama->EditAttributes() ?>>
</span>
<?php echo $tb_akun_lvl4->akun_lvl4_nama->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php if (!$tb_akun_lvl4_edit->IsModal) { ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $tb_akun_lvl4_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
<?php } ?>
</form>
<script type="text/javascript">
ftb_akun_lvl4edit.Init();
</script>
<?php
$tb_akun_lvl4_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tb_akun_lvl4_edit->Page_Terminate();
?>
