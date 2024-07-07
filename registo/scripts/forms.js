// Check to see if e-mail isn't blank and is well formed
// Read more at http://www.marketingtechblog.com/javascript-regex-emailaddress/#ixzz1p1ZDMNZe
var filter;
filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,3})$/;
//filter = /^([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})$/i;
var filterName;
filterName = /^[a-zA-Z]{8,32}$/;
var filterPassword;
filterPassword = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/;


// Validate the login form
function FormLoginValidator(theForm) {
  // Check to see if name isn't blank
  if ( theForm.name.value === "" ) {
    alert("You must enter a VALID name.");
    theForm.name.focus();
    return false;
  }
  
  if ( !filter.test( theForm.email.value ) ) {
    alert('Please provide a valid e-mail address');
    theForm.email.focus();
    return false;
  }
  
  return true;
}

function ValidateNamePass(filter, elementName, msg){
  var formElement;
  formElement = document.getElementById(elementName).value;
  
  var msgElement;
  msgElement = document.getElementById(elementName + "ID");

  if(!filter.test(formElement)){
    msgElement.innerHTML = msg;
    return false;
  }
  else{
    msgElement.innerHTML = "";
    return true;
  }

}

function validadeRadioGroup(groupName, msg) {
  var radios;
  radios = document.getElementsByName( groupName );
  
  var result = false;
  
  for (var idxCurrentRadio = 0; idxCurrentRadio < radios.length; idxCurrentRadio++) {
    var currentRadio;
    currentRadio = radios[ idxCurrentRadio ];
    
    if ( currentRadio.checked ) {
      result = true;
      break;
    }
  }
  
  var msgElement;
  msgElement = document.getElementById(groupName + "ID");
  
  if ( result===false ) {
    msgElement.innerHTML = msg;
  }
  else {
    msgElement.innerHTML = "";
  }

  return result;
}

function validadeSelect(selectName) {
  var selectedValue;
  selectedValue = document.getElementById(selectName + "ID").value;
  
  if ( selectedValue==="" || selectedValue==="0" || selectedValue==="-1" ) {
    return false;
  }
  else {
    return true;
  }
}

function FormUpdateProfileValidator(theForm) {

  var msgUserName = "The usermame can only have leters and a minimum length of 8 - and maximum 32";
  
  if (ValidateNamePass(filterName, "alias", msgUserName) === false){
    theForm.alias.focus();
    return false;
  }

  var msgPassword = "The password must contain numbers and letters, minimum size 8-32"

  if (ValidateNamePass(filterPassword, "password", msgPassword) === false){
    theForm.alias.focus();
    return false;
  }

  // Ensure that there is only radio button selected
  if ( validadeRadioGroup( "age", "You must select an age" )===false ) {
    return false;
  }
  
  var msg;
  msg = document.getElementById( "locationMessageID" );
  if ( 
          !validadeSelect( "district" ) || 
          !validadeSelect( "county" ) || 
          !validadeSelect( "location" ) || 
          !validadeSelect( "postalCode" ) || 
          !validadeSelect( "postalCodeExtention" ) ) {
    msg.innerHTML = "You must select a zip code";
    return false;
  }
  else {
    msg.innerHTML = "";
  }

  alert( 'Home work for the students!' );
  return true;
}

var xmlHttp;

function GetXmlHttpObject() {
  try {
    return new ActiveXObject("Msxml2.XMLHTTP");
  } catch(e) {} // Internet Explorer
  try {
    return new ActiveXObject("Microsoft.XMLHTTP");
  } catch(e) {} // Internet Explorer
  try {
    return new XMLHttpRequest();
  } catch(e) {} // Firefox, Opera 8.0+, Safari
  alert("XMLHttpRequest not supported");
  return null;
}

// The District Select has change
function SelectDistrictChange(theSelect) {
  // The new option
  var selectedDistrict = theSelect.value;
  
  // The new image to display
  var districtImageFile = "images/distritos/" + selectedDistrict + ".gif";
  document.getElementById("imgDistrict").src = districtImageFile;

  // Preparing the arguments to request the counties
  var args = "district="+selectedDistrict;
  
  // With HTTP GET method
  xmlHttp = GetXmlHttpObject();
  xmlHttp.open("GET", "getCounties.php?"+args, true);
  xmlHttp.onreadystatechange=SelectDistrictHandleReply;
  xmlHttp.send(null);
}

//Fill in the counties for the new district
function SelectDistrictHandleReply() {
  
  //alert( xmlHttp.readyState );
  
  if( xmlHttp.readyState === 4 ) {
    var countySelect=document.getElementById("county");

    countySelect.options.length = 0;

    //alert( xmlHttp.responseText );
    
    var counties = JSON.parse( xmlHttp.responseText );
    
    //alert( counties );

    for (i=0; i<counties.length; i++) {
      var currentCounty = counties[i];
      
      var value  = currentCounty.idCounty;
      var option = currentCounty.nameCounty;
	  
      try{
        countySelect.add( new Option("", value), null);
      }
      catch(e) {
        countySelect.add( new Option("", value) );
      }
      
      countySelect.options[i].innerHTML = option;
    }
  }
}

//The County Select has change
function SelectCountyChange(theSelect) {
  // The new option
  var selectedCounty = theSelect.value;
  
  var selectedDistrict = document.getElementById( "district" ).value;
  
  // Preparing the arguments to request the zip codes
  var args = "county=" + selectedCounty + "&district=" + selectedDistrict;
  
  xmlHttp = GetXmlHttpObject();
  
  // Using HTTP GET method
  //xmlHttp.open("GET", "getZips.php?"+args, true);
  //xmlHttp.onreadystatechange=SelectCountyHandleReply;
  //xmlHttp.send( null );
  
  // Using HTTP POST method
  xmlHttp.open("POST", "getZips.php", true);
  xmlHttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded");
  xmlHttp.onreadystatechange=SelectCountyHandleReply;
  // ensure args is encoded!
  xmlHttp.send( args ); 
}

//Fill in the Zips for the new county
function SelectCountyHandleReply() {
  
  if( xmlHttp.readyState === 4 ) {
    var zipSelect=document.getElementById("zip");

    for(var count = zipSelect.options.length - 1; count >= 0; count--) {
      zipSelect.options[count] = null;
    }

    var zips = JSON.parse( xmlHttp.responseText );
    
    for (i=0; i<zips.length; i++) {

      var currentZip = zips[i];
      
      var value  = currentZip.id;
      var option = currentZip.value;

      try{
        zipSelect.add( new Option(option, value), null);
      }
      catch(e) {
        zipSelect.add( new Option(option, value) );
      }
    }
  }
}
