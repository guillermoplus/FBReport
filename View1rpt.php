<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg10.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn10.php" ?>
<?php include_once "phprptinc/ewrusrfn10.php" ?>
<?php include_once "View1rptinfo.php" ?>
<?php

//
// Page class
//

$View1_rpt = NULL; // Initialize page object first

class crView1_rpt extends crView1 {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{7137060F-07C2-4A35-A0C6-B1239DE3EB7E}";

	// Page object name
	var $PageObjName = 'View1_rpt';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
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
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (View1)
		if (!isset($GLOBALS["View1"])) {
			$GLOBALS["View1"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["View1"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'rpt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'View1', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fView1rpt";

		// Generate report options
		$this->GenerateOptions = new crListOptions();
		$this->GenerateOptions->Tag = "div";
		$this->GenerateOptions->TagClassName = "ewGenerateOption";
	}

	//
	// Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		// Setup export options

		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Security, $ReportLanguage, $ReportOptions;
		$exportid = session_id();
		$ReportTypes = array();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = FALSE;
		$ReportTypes["print"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPrint") : "";

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["excel"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormExcel") : "";

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = FALSE;
		$item->Visible = FALSE;
		$ReportTypes["word"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormWord") : "";

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = FALSE;

		$ReportTypes["pdf"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPdf") : "";

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_View1\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_View1',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;
		$ReportTypes["email"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormEmail") : "";
		$ReportOptions["ReportTypes"] = $ReportTypes;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fView1rpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fView1rpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	// Set up search options
	function SetupSearchOptions() {
		global $ReportLanguage;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = $this->FilterApplied ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fView1rpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = FALSE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = FALSE && $this->FilterApplied;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->SearchOptions->HideAllOptions();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();
			if (ob_get_length())
				ob_end_clean();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				if (@$this->GenOptions["reporttype"] == "email") {
					$saveResponse = $this->$fn($sContent, $this->GenOptions);
					$this->WriteGenResponse($saveResponse);
				} else {
					echo $this->$fn($sContent, array());
				}
				$url = ""; // Avoid redirect
			} else {
				$saveToFile = $this->$fn($sContent, $this->GenOptions);
				if (@$this->GenOptions["reporttype"] <> "") {
					$saveUrl = ($saveToFile <> "") ? ewr_ConvertFullUrl($saveToFile) : $ReportLanguage->Phrase("GenerateSuccess");
					$this->WriteGenResponse($saveUrl);
					$url = ""; // Avoid redirect
				}
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 5; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpColumnCount = 0;
	var $SubGrpColumnCount = 0;
	var $DtlColumnCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;
	var $DetailRows = array();

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Set field visibility for detail fields
		$this->product_s_desc->SetVisibility();
		$this->product_desc->SetVisibility();
		$this->product_name->SetVisibility();
		$this->slug->SetVisibility();
		$this->product_sku->SetVisibility();
		$this->virtuemart_product_id->SetVisibility();
		$this->product_url->SetVisibility();
		$this->category_name->SetVisibility();
		$this->file_url->SetVisibility();
		$this->file_type->SetVisibility();
		$this->product_price->SetVisibility();
		$this->mf_name->SetVisibility();

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 13;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// No filter
		$this->FilterApplied = FALSE;
		$this->FilterOptions->GetItem("savecurrentfilter")->Visible = FALSE;
		$this->FilterOptions->GetItem("deletefilter")->Visible = FALSE;

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);

		// Search options
		$this->SetupSearchOptions();

		// Get sort
		$this->Sort = $this->GetSort($this->GenOptions);

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = ($this->TotalGrps > 0);

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
			$this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup($this->GenOptions);

		// Set no record found message
		if ($this->TotalGrps == 0) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
			$this->GenerateOptions->HideAllOptions();
		}

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		$conn = &$this->Connection();
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row
				$this->FirstRowData = array();
				$this->FirstRowData['product_name'] = ewr_Conv($rs->fields('product_name'), 200);
				$this->FirstRowData['slug'] = ewr_Conv($rs->fields('slug'), 200);
				$this->FirstRowData['product_sku'] = ewr_Conv($rs->fields('product_sku'), 200);
				$this->FirstRowData['virtuemart_product_id'] = ewr_Conv($rs->fields('virtuemart_product_id'), 19);
				$this->FirstRowData['product_url'] = ewr_Conv($rs->fields('product_url'), 200);
				$this->FirstRowData['category_name'] = ewr_Conv($rs->fields('category_name'), 200);
				$this->FirstRowData['file_type'] = ewr_Conv($rs->fields('file_type'), 200);
				$this->FirstRowData['product_price'] = ewr_Conv($rs->fields('product_price'), 131);
				$this->FirstRowData['mf_name'] = ewr_Conv($rs->fields('mf_name'), 200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->product_s_desc->setDbValue($rs->fields('product_s_desc'));
			$this->product_desc->setDbValue($rs->fields('product_desc'));
			$this->product_name->setDbValue($rs->fields('product_name'));
			$this->slug->setDbValue($rs->fields('slug'));
			$this->product_sku->setDbValue($rs->fields('product_sku'));
			$this->virtuemart_product_id->setDbValue($rs->fields('virtuemart_product_id'));
			$this->product_url->setDbValue($rs->fields('product_url'));
			$this->category_name->setDbValue($rs->fields('category_name'));
			$this->file_url->setDbValue($rs->fields('file_url'));
			$this->file_type->setDbValue($rs->fields('file_type'));
			$this->product_price->setDbValue($rs->fields('product_price'));
			$this->mf_name->setDbValue($rs->fields('mf_name'));
			$this->Val[1] = $this->product_s_desc->CurrentValue;
			$this->Val[2] = $this->product_desc->CurrentValue;
			$this->Val[3] = $this->product_name->CurrentValue;
			$this->Val[4] = $this->slug->CurrentValue;
			$this->Val[5] = $this->product_sku->CurrentValue;
			$this->Val[6] = $this->virtuemart_product_id->CurrentValue;
			$this->Val[7] = $this->product_url->CurrentValue;
			$this->Val[8] = $this->category_name->CurrentValue;
			$this->Val[9] = $this->file_url->CurrentValue;
			$this->Val[10] = $this->file_type->CurrentValue;
			$this->Val[11] = $this->product_price->CurrentValue;
			$this->Val[12] = $this->mf_name->CurrentValue;
		} else {
			$this->product_s_desc->setDbValue("");
			$this->product_desc->setDbValue("");
			$this->product_name->setDbValue("");
			$this->slug->setDbValue("");
			$this->product_sku->setDbValue("");
			$this->virtuemart_product_id->setDbValue("");
			$this->product_url->setDbValue("");
			$this->category_name->setDbValue("");
			$this->file_url->setDbValue("");
			$this->file_type->setDbValue("");
			$this->product_price->setDbValue("");
			$this->mf_name->setDbValue("");
		}
	}

	// Set up starting group
	function SetUpStartGroup($options = array()) {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;
		$startGrp = (@$options["start"] <> "") ? $options["start"] : @$_GET[EWR_TABLE_START_GROUP];
		$pageNo = (@$options["pageno"] <> "") ? $options["pageno"] : @$_GET["pageno"];

		// Check for a 'start' parameter
		if ($startGrp != "") {
			$this->StartGrp = $startGrp;
			$this->setStartGroup($this->StartGrp);
		} elseif ($pageNo != "") {
			$nPageNo = $pageNo;
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Output data as Json

			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				if (ob_get_length())
					ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 5; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 5; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if (!$this->GrandSummarySetup) { // Get Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}
		$bGotSummary = TRUE;

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL && !($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER)) { // Summary row
			ewr_PrependClass($this->RowAttrs["class"], ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel); // Set up row class

			// product_s_desc
			$this->product_s_desc->HrefValue = "";

			// product_desc
			$this->product_desc->HrefValue = "";

			// product_name
			$this->product_name->HrefValue = "";

			// slug
			$this->slug->HrefValue = "";

			// product_sku
			$this->product_sku->HrefValue = "";

			// virtuemart_product_id
			$this->virtuemart_product_id->HrefValue = "";

			// product_url
			$this->product_url->HrefValue = "";

			// category_name
			$this->category_name->HrefValue = "";

			// file_url
			$this->file_url->HrefValue = "";

			// file_type
			$this->file_type->HrefValue = "";

			// product_price
			$this->product_price->HrefValue = "";

			// mf_name
			$this->mf_name->HrefValue = "";
		} else {
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER) {
			} else {
			}

			// product_s_desc
			$this->product_s_desc->ViewValue = $this->product_s_desc->CurrentValue;
			$this->product_s_desc->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_desc
			$this->product_desc->ViewValue = $this->product_desc->CurrentValue;
			$this->product_desc->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_name
			$this->product_name->ViewValue = $this->product_name->CurrentValue;
			$this->product_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// slug
			$this->slug->ViewValue = $this->slug->CurrentValue;
			$this->slug->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_sku
			$this->product_sku->ViewValue = $this->product_sku->CurrentValue;
			$this->product_sku->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// virtuemart_product_id
			$this->virtuemart_product_id->ViewValue = $this->virtuemart_product_id->CurrentValue;
			$this->virtuemart_product_id->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_url
			$this->product_url->ViewValue = $this->product_url->CurrentValue;
			$this->product_url->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// category_name
			$this->category_name->ViewValue = $this->category_name->CurrentValue;
			$this->category_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// file_url
			$this->file_url->ViewValue = $this->file_url->CurrentValue;
			$this->file_url->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// file_type
			$this->file_type->ViewValue = $this->file_type->CurrentValue;
			$this->file_type->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_price
			$this->product_price->ViewValue = $this->product_price->CurrentValue;
			$this->product_price->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// mf_name
			$this->mf_name->ViewValue = $this->mf_name->CurrentValue;
			$this->mf_name->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// product_s_desc
			$this->product_s_desc->HrefValue = "";

			// product_desc
			$this->product_desc->HrefValue = "";

			// product_name
			$this->product_name->HrefValue = "";

			// slug
			$this->slug->HrefValue = "";

			// product_sku
			$this->product_sku->HrefValue = "";

			// virtuemart_product_id
			$this->virtuemart_product_id->HrefValue = "";

			// product_url
			$this->product_url->HrefValue = "";

			// category_name
			$this->category_name->HrefValue = "";

			// file_url
			$this->file_url->HrefValue = "";

			// file_type
			$this->file_type->HrefValue = "";

			// product_price
			$this->product_price->HrefValue = "";

			// mf_name
			$this->mf_name->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// product_s_desc
			$CurrentValue = $this->product_s_desc->CurrentValue;
			$ViewValue = &$this->product_s_desc->ViewValue;
			$ViewAttrs = &$this->product_s_desc->ViewAttrs;
			$CellAttrs = &$this->product_s_desc->CellAttrs;
			$HrefValue = &$this->product_s_desc->HrefValue;
			$LinkAttrs = &$this->product_s_desc->LinkAttrs;
			$this->Cell_Rendered($this->product_s_desc, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// product_desc
			$CurrentValue = $this->product_desc->CurrentValue;
			$ViewValue = &$this->product_desc->ViewValue;
			$ViewAttrs = &$this->product_desc->ViewAttrs;
			$CellAttrs = &$this->product_desc->CellAttrs;
			$HrefValue = &$this->product_desc->HrefValue;
			$LinkAttrs = &$this->product_desc->LinkAttrs;
			$this->Cell_Rendered($this->product_desc, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// product_name
			$CurrentValue = $this->product_name->CurrentValue;
			$ViewValue = &$this->product_name->ViewValue;
			$ViewAttrs = &$this->product_name->ViewAttrs;
			$CellAttrs = &$this->product_name->CellAttrs;
			$HrefValue = &$this->product_name->HrefValue;
			$LinkAttrs = &$this->product_name->LinkAttrs;
			$this->Cell_Rendered($this->product_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// slug
			$CurrentValue = $this->slug->CurrentValue;
			$ViewValue = &$this->slug->ViewValue;
			$ViewAttrs = &$this->slug->ViewAttrs;
			$CellAttrs = &$this->slug->CellAttrs;
			$HrefValue = &$this->slug->HrefValue;
			$LinkAttrs = &$this->slug->LinkAttrs;
			$this->Cell_Rendered($this->slug, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// product_sku
			$CurrentValue = $this->product_sku->CurrentValue;
			$ViewValue = &$this->product_sku->ViewValue;
			$ViewAttrs = &$this->product_sku->ViewAttrs;
			$CellAttrs = &$this->product_sku->CellAttrs;
			$HrefValue = &$this->product_sku->HrefValue;
			$LinkAttrs = &$this->product_sku->LinkAttrs;
			$this->Cell_Rendered($this->product_sku, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// virtuemart_product_id
			$CurrentValue = $this->virtuemart_product_id->CurrentValue;
			$ViewValue = &$this->virtuemart_product_id->ViewValue;
			$ViewAttrs = &$this->virtuemart_product_id->ViewAttrs;
			$CellAttrs = &$this->virtuemart_product_id->CellAttrs;
			$HrefValue = &$this->virtuemart_product_id->HrefValue;
			$LinkAttrs = &$this->virtuemart_product_id->LinkAttrs;
			$this->Cell_Rendered($this->virtuemart_product_id, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// product_url
			$CurrentValue = $this->product_url->CurrentValue;
			$ViewValue = &$this->product_url->ViewValue;
			$ViewAttrs = &$this->product_url->ViewAttrs;
			$CellAttrs = &$this->product_url->CellAttrs;
			$HrefValue = &$this->product_url->HrefValue;
			$LinkAttrs = &$this->product_url->LinkAttrs;
			$this->Cell_Rendered($this->product_url, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// category_name
			$CurrentValue = $this->category_name->CurrentValue;
			$ViewValue = &$this->category_name->ViewValue;
			$ViewAttrs = &$this->category_name->ViewAttrs;
			$CellAttrs = &$this->category_name->CellAttrs;
			$HrefValue = &$this->category_name->HrefValue;
			$LinkAttrs = &$this->category_name->LinkAttrs;
			$this->Cell_Rendered($this->category_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// file_url
			$CurrentValue = $this->file_url->CurrentValue;
			$ViewValue = &$this->file_url->ViewValue;
			$ViewAttrs = &$this->file_url->ViewAttrs;
			$CellAttrs = &$this->file_url->CellAttrs;
			$HrefValue = &$this->file_url->HrefValue;
			$LinkAttrs = &$this->file_url->LinkAttrs;
			$this->Cell_Rendered($this->file_url, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// file_type
			$CurrentValue = $this->file_type->CurrentValue;
			$ViewValue = &$this->file_type->ViewValue;
			$ViewAttrs = &$this->file_type->ViewAttrs;
			$CellAttrs = &$this->file_type->CellAttrs;
			$HrefValue = &$this->file_type->HrefValue;
			$LinkAttrs = &$this->file_type->LinkAttrs;
			$this->Cell_Rendered($this->file_type, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// product_price
			$CurrentValue = $this->product_price->CurrentValue;
			$ViewValue = &$this->product_price->ViewValue;
			$ViewAttrs = &$this->product_price->ViewAttrs;
			$CellAttrs = &$this->product_price->CellAttrs;
			$HrefValue = &$this->product_price->HrefValue;
			$LinkAttrs = &$this->product_price->LinkAttrs;
			$this->Cell_Rendered($this->product_price, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// mf_name
			$CurrentValue = $this->mf_name->CurrentValue;
			$ViewValue = &$this->mf_name->ViewValue;
			$ViewAttrs = &$this->mf_name->ViewAttrs;
			$CellAttrs = &$this->mf_name->CellAttrs;
			$HrefValue = &$this->mf_name->HrefValue;
			$LinkAttrs = &$this->mf_name->LinkAttrs;
			$this->Cell_Rendered($this->mf_name, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpColumnCount = 0;
		$this->SubGrpColumnCount = 0;
		$this->DtlColumnCount = 0;
		if ($this->product_s_desc->Visible) $this->DtlColumnCount += 1;
		if ($this->product_desc->Visible) $this->DtlColumnCount += 1;
		if ($this->product_name->Visible) $this->DtlColumnCount += 1;
		if ($this->slug->Visible) $this->DtlColumnCount += 1;
		if ($this->product_sku->Visible) $this->DtlColumnCount += 1;
		if ($this->virtuemart_product_id->Visible) $this->DtlColumnCount += 1;
		if ($this->product_url->Visible) $this->DtlColumnCount += 1;
		if ($this->category_name->Visible) $this->DtlColumnCount += 1;
		if ($this->file_url->Visible) $this->DtlColumnCount += 1;
		if ($this->file_type->Visible) $this->DtlColumnCount += 1;
		if ($this->product_price->Visible) $this->DtlColumnCount += 1;
		if ($this->mf_name->Visible) $this->DtlColumnCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("rpt", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage, $ReportOptions;
		$ReportTypes = $ReportOptions["ReportTypes"];
		$ReportOptions["ReportTypes"] = $ReportTypes;
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort($options = array()) {
		if ($this->DrillDown)
			return "";
		$bResetSort = @$options["resetsort"] == "1" || @$_GET["cmd"] == "resetsort";
		$orderBy = (@$options["order"] <> "") ? @$options["order"] : ewr_StripSlashes(@$_GET["order"]);
		$orderType = (@$options["ordertype"] <> "") ? @$options["ordertype"] : ewr_StripSlashes(@$_GET["ordertype"]);

		// Check for a resetsort command
		if ($bResetSort) {
			$this->setOrderBy("");
			$this->setStartGroup(1);
			$this->product_s_desc->setSort("");
			$this->product_desc->setSort("");
			$this->product_name->setSort("");
			$this->slug->setSort("");
			$this->product_sku->setSort("");
			$this->virtuemart_product_id->setSort("");
			$this->product_url->setSort("");
			$this->category_name->setSort("");
			$this->file_url->setSort("");
			$this->file_type->setSort("");
			$this->product_price->setSort("");
			$this->mf_name->setSort("");

		// Check for an Order parameter
		} elseif ($orderBy <> "") {
			$this->CurrentOrder = $orderBy;
			$this->CurrentOrderType = $orderType;
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export to EXCEL
	function ExportExcel($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
			echo $html;
		}
		return $saveToFile;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
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
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($View1_rpt)) $View1_rpt = new crView1_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$View1_rpt;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "") { ?>
<script type="text/javascript">

// Create page object
var View1_rpt = new ewr_Page("View1_rpt");

// Page properties
View1_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = View1_rpt.PageID;

// Extend page with Chart_Rendering function
View1_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
View1_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<?php if (@$Page->GenOptions["showfilter"] == "1") { ?>
<?php $Page->ShowFilterList(TRUE) ?>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
	$Page->GenerateOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<div id="report_summary">
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->product_s_desc->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_s_desc"><div class="View1_product_s_desc"><span class="ewTableHeaderCaption"><?php echo $Page->product_s_desc->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_s_desc">
<?php if ($Page->SortUrl($Page->product_s_desc) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_s_desc">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_s_desc->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_s_desc" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_s_desc) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_s_desc->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_s_desc->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_s_desc->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->product_desc->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_desc"><div class="View1_product_desc"><span class="ewTableHeaderCaption"><?php echo $Page->product_desc->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_desc">
<?php if ($Page->SortUrl($Page->product_desc) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_desc">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_desc->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_desc" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_desc) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_desc->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_desc->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_desc->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->product_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_name"><div class="View1_product_name"><span class="ewTableHeaderCaption"><?php echo $Page->product_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_name">
<?php if ($Page->SortUrl($Page->product_name) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->slug->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="slug"><div class="View1_slug"><span class="ewTableHeaderCaption"><?php echo $Page->slug->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="slug">
<?php if ($Page->SortUrl($Page->slug) == "") { ?>
		<div class="ewTableHeaderBtn View1_slug">
			<span class="ewTableHeaderCaption"><?php echo $Page->slug->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_slug" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->slug) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->slug->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->slug->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->slug->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->product_sku->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_sku"><div class="View1_product_sku"><span class="ewTableHeaderCaption"><?php echo $Page->product_sku->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_sku">
<?php if ($Page->SortUrl($Page->product_sku) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_sku">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_sku->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_sku" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_sku) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_sku->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_sku->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_sku->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->virtuemart_product_id->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="virtuemart_product_id"><div class="View1_virtuemart_product_id"><span class="ewTableHeaderCaption"><?php echo $Page->virtuemart_product_id->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="virtuemart_product_id">
<?php if ($Page->SortUrl($Page->virtuemart_product_id) == "") { ?>
		<div class="ewTableHeaderBtn View1_virtuemart_product_id">
			<span class="ewTableHeaderCaption"><?php echo $Page->virtuemart_product_id->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_virtuemart_product_id" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->virtuemart_product_id) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->virtuemart_product_id->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->virtuemart_product_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->virtuemart_product_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->product_url->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_url"><div class="View1_product_url"><span class="ewTableHeaderCaption"><?php echo $Page->product_url->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_url">
<?php if ($Page->SortUrl($Page->product_url) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_url">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_url->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_url" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_url) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_url->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_url->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_url->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->category_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="category_name"><div class="View1_category_name"><span class="ewTableHeaderCaption"><?php echo $Page->category_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="category_name">
<?php if ($Page->SortUrl($Page->category_name) == "") { ?>
		<div class="ewTableHeaderBtn View1_category_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->category_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_category_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->category_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->category_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->category_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->category_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->file_url->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="file_url"><div class="View1_file_url"><span class="ewTableHeaderCaption"><?php echo $Page->file_url->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="file_url">
<?php if ($Page->SortUrl($Page->file_url) == "") { ?>
		<div class="ewTableHeaderBtn View1_file_url">
			<span class="ewTableHeaderCaption"><?php echo $Page->file_url->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_file_url" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->file_url) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->file_url->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->file_url->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->file_url->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->file_type->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="file_type"><div class="View1_file_type"><span class="ewTableHeaderCaption"><?php echo $Page->file_type->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="file_type">
<?php if ($Page->SortUrl($Page->file_type) == "") { ?>
		<div class="ewTableHeaderBtn View1_file_type">
			<span class="ewTableHeaderCaption"><?php echo $Page->file_type->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_file_type" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->file_type) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->file_type->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->file_type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->file_type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->product_price->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="product_price"><div class="View1_product_price"><span class="ewTableHeaderCaption"><?php echo $Page->product_price->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="product_price">
<?php if ($Page->SortUrl($Page->product_price) == "") { ?>
		<div class="ewTableHeaderBtn View1_product_price">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_price->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_product_price" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->product_price) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->product_price->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->product_price->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->product_price->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->mf_name->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="mf_name"><div class="View1_mf_name"><span class="ewTableHeaderCaption"><?php echo $Page->mf_name->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="mf_name">
<?php if ($Page->SortUrl($Page->mf_name) == "") { ?>
		<div class="ewTableHeaderBtn View1_mf_name">
			<span class="ewTableHeaderCaption"><?php echo $Page->mf_name->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer View1_mf_name" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->mf_name) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->mf_name->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->mf_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->mf_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;
?>
<?php

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->product_s_desc->Visible) { ?>
		<td data-field="product_s_desc"<?php echo $Page->product_s_desc->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_s_desc"<?php echo $Page->product_s_desc->ViewAttributes() ?>><?php echo $Page->product_s_desc->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->product_desc->Visible) { ?>
		<td data-field="product_desc"<?php echo $Page->product_desc->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_desc"<?php echo $Page->product_desc->ViewAttributes() ?>><?php echo $Page->product_desc->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->product_name->Visible) { ?>
		<td data-field="product_name"<?php echo $Page->product_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_name"<?php echo $Page->product_name->ViewAttributes() ?>><?php echo $Page->product_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->slug->Visible) { ?>
		<td data-field="slug"<?php echo $Page->slug->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_slug"<?php echo $Page->slug->ViewAttributes() ?>><?php echo $Page->slug->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->product_sku->Visible) { ?>
		<td data-field="product_sku"<?php echo $Page->product_sku->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_sku"<?php echo $Page->product_sku->ViewAttributes() ?>><?php echo $Page->product_sku->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->virtuemart_product_id->Visible) { ?>
		<td data-field="virtuemart_product_id"<?php echo $Page->virtuemart_product_id->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_virtuemart_product_id"<?php echo $Page->virtuemart_product_id->ViewAttributes() ?>><?php echo $Page->virtuemart_product_id->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->product_url->Visible) { ?>
		<td data-field="product_url"<?php echo $Page->product_url->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_url"<?php echo $Page->product_url->ViewAttributes() ?>><?php echo $Page->product_url->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->category_name->Visible) { ?>
		<td data-field="category_name"<?php echo $Page->category_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_category_name"<?php echo $Page->category_name->ViewAttributes() ?>><?php echo $Page->category_name->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->file_url->Visible) { ?>
		<td data-field="file_url"<?php echo $Page->file_url->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_file_url"<?php echo $Page->file_url->ViewAttributes() ?>><?php echo $Page->file_url->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->file_type->Visible) { ?>
		<td data-field="file_type"<?php echo $Page->file_type->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_file_type"<?php echo $Page->file_type->ViewAttributes() ?>><?php echo $Page->file_type->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->product_price->Visible) { ?>
		<td data-field="product_price"<?php echo $Page->product_price->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_product_price"<?php echo $Page->product_price->ViewAttributes() ?>><?php echo $Page->product_price->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->mf_name->Visible) { ?>
		<td data-field="mf_name"<?php echo $Page->mf_name->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_View1_mf_name"<?php echo $Page->mf_name->ViewAttributes() ?>><?php echo $Page->mf_name->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && FALSE) { // No header displayed ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<!-- Report grid (begin) -->
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || FALSE) { // Show footer ?>
</table>
</div>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "View1rptpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
</div>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
