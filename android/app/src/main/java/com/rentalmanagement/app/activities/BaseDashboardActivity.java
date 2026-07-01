package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;

import androidx.annotation.Nullable;

import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.preferences.SessionManager;
import com.rentalmanagement.app.repository.AuthRepository;
import com.rentalmanagement.app.utilities.NavigationUtils;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public abstract class BaseDashboardActivity extends BaseActivity {

    protected SessionManager sessionManager;
    protected AuthRepository authRepository;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        sessionManager = new SessionManager(this);
        authRepository = new AuthRepository();
    }

    protected void logout() {
        String token = sessionManager.getToken();
        if (token == null) {
            clearAndGoLogin();
            return;
        }

        authRepository.logout("Bearer " + token).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                clearAndGoLogin();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                clearAndGoLogin();
            }
        });
    }

    protected void clearAndGoLogin() {
        sessionManager.clearSession();
        Intent intent = NavigationUtils.loginIntent(this);
        intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        startActivity(intent);
        finish();
    }
}
