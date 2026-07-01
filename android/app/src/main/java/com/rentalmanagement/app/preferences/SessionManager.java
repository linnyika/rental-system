package com.rentalmanagement.app.preferences;

import android.content.Context;
import android.content.SharedPreferences;
import android.text.TextUtils;

import com.rentalmanagement.app.constants.Constants;

public class SessionManager {

    private final SharedPreferences sharedPreferences;

    public SessionManager(Context context) {
        sharedPreferences = context.getSharedPreferences(Constants.PREFS_NAME, Context.MODE_PRIVATE);
    }

    public void saveSession(String token, long userId, String role, String userName) {
        sharedPreferences.edit()
                .putString(Constants.PREF_TOKEN, token)
                .putLong(Constants.PREF_USER_ID, userId)
                .putString(Constants.PREF_USER_ROLE, role)
                .putString(Constants.PREF_USER_NAME, userName)
                .apply();
    }

    public String getToken() {
        return sharedPreferences.getString(Constants.PREF_TOKEN, null);
    }

    public long getUserId() {
        return sharedPreferences.getLong(Constants.PREF_USER_ID, -1L);
    }

    public String getRole() {
        return sharedPreferences.getString(Constants.PREF_USER_ROLE, null);
    }

    public String getUserName() {
        return sharedPreferences.getString(Constants.PREF_USER_NAME, null);
    }

    public boolean isLoggedIn() {
        return !TextUtils.isEmpty(getToken());
    }

    public void clearSession() {
        sharedPreferences.edit().clear().apply();
    }
}
