;;;; error code category
;; 
;; 0001 ~ 1000 common error, session
;; 1001 ~ 2000 login, profile, security, plant account
;; 2001 ~ 3000 unit, relay
;; 3001 ~ 4000 alert, alert contact
;; 4001 ~ 5000 upload, sms, numerex, parser
;; 
;;;;

[err_code]
;;0001 ~ 1000
0001 = "Unknown error."
0002 = "Invalid parameters."
0003 = "Database connection error."

;;1001 ~ 2000
1001 = "Invalid login account."
1002 = "Invalid password."
1003 = "This login session is currently in used, or it was never logged out."
1004 = "This login session is currently in used, and system can\'t force to logout."
1005 = "Can't create login session."
1006 = "User's profile missed." 
1007 = "Uploaded logo file is missed."
1008 = "Account's profile is not found."
1009 = "Error occurs when uploading logo file."
1010 = "Update logo info fail."
1011 = "Find no logo info."
1012 = "Error occurs when adding account information."
1013 = "Error occurs when creating login account."
1014 = "Error occurs when adding company information."
1015 = "Company information has not been updated. The submitted data might be unchanged."
1016 = "User information has not been updated. The submitted data might be unchanged."
1017 = "Fail to delete login user."
1018 = "Access deny for this account."

;;2001 ~ 3000
2001 = "Schedule profile is not found."
2002 = "Delete schedule profile fail."
2003 = "Can't add schedule profile."
2004 = "Error occurs when adding controller information."