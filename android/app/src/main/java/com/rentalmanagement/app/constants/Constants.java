package com.rentalmanagement.app.constants;

//import com.rentalmanagement.app.BuildConfig;

public final class Constants {

    private Constants() {
    }

    // Update the BASE_URL in app/build.gradle before production deployment.
   public static final String BASE_URL = "http://10.0.2.2:8000/api/";
    public static final String PREFS_NAME = "rental_management_session";
    public static final String PREF_TOKEN = "pref_token";
    public static final String PREF_USER_ID = "pref_user_id";
    public static final String PREF_USER_ROLE = "pref_user_role";
    public static final String PREF_USER_NAME = "pref_user_name";
    public static final int API_TIMEOUT_SECONDS = 30;
}
