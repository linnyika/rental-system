package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;

import com.rentalmanagement.app.databinding.ActivityMainBinding;
import com.rentalmanagement.app.models.CurrentUserResponse;
import com.rentalmanagement.app.preferences.SessionManager;
import com.rentalmanagement.app.repository.AuthRepository;
import com.rentalmanagement.app.utilities.NavigationUtils;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MainActivity extends BaseActivity {

    private ActivityMainBinding binding;
    private SessionManager sessionManager;
    private AuthRepository authRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityMainBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        sessionManager = new SessionManager(this);
        authRepository = new AuthRepository();

        new Handler(Looper.getMainLooper()).postDelayed(this::routeAfterSplash, 2000L);
    }

    private void routeAfterSplash() {
        if (!sessionManager.isLoggedIn()) {
            openLogin();
            return;
        }

        String token = sessionManager.getToken();
        String role = sessionManager.getRole();

        if (!isNetworkAvailable()) {
            openDashboard(role);
            return;
        }

        authRepository.currentUser("Bearer " + token).enqueue(new Callback<CurrentUserResponse>() {
            @Override
            public void onResponse(Call<CurrentUserResponse> call, Response<CurrentUserResponse> response) {
                if (response.isSuccessful() && response.body() != null && response.body().getUser() != null) {
                    sessionManager.saveSession(
                            token,
                            response.body().getUser().getId(),
                            response.body().getUser().getRole(),
                            response.body().getUser().getName()
                    );
                    openDashboard(response.body().getUser().getRole());
                    return;
                }

                if (response.code() == 401) {
                    sessionManager.clearSession();
                    openLogin();
                    return;
                }

                openDashboard(role);
            }

            @Override
            public void onFailure(Call<CurrentUserResponse> call, Throwable t) {
                openDashboard(role);
            }
        });
    }

    private void openLogin() {
        Intent intent = NavigationUtils.loginIntent(this);
        startActivity(intent);
        finish();
    }

    private void openDashboard(String role) {
        Intent intent = NavigationUtils.dashboardIntent(this, role);
        startActivity(intent);
        finish();
    }
}
