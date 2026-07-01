package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;

import com.rentalmanagement.app.databinding.ActivityLoginBinding;
import com.rentalmanagement.app.models.AuthResponse;
import com.rentalmanagement.app.models.LoginRequest;
import com.rentalmanagement.app.preferences.SessionManager;
import com.rentalmanagement.app.repository.AuthRepository;
import com.rentalmanagement.app.utilities.NavigationUtils;

import java.io.IOException;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends BaseActivity {

    private ActivityLoginBinding binding;
    private SessionManager sessionManager;
    private AuthRepository authRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityLoginBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        sessionManager = new SessionManager(this);
        authRepository = new AuthRepository();

        binding.btnLogin.setOnClickListener(v -> attemptLogin());
        binding.tvSignUp.setOnClickListener(v -> {
            Intent intent = new Intent(LoginActivity.this, LandlordSignupActivity.class);
            startActivity(intent);
        });
    }

    private void attemptLogin() {
        clearErrors();

        String email = binding.etEmail.getText() == null ? "" : binding.etEmail.getText().toString().trim();
        String password = binding.etPassword.getText() == null ? "" : binding.etPassword.getText().toString().trim();

        boolean valid = true;

        if (TextUtils.isEmpty(email)) {
            binding.emailInputLayout.setError("Email is required");
            valid = false;
        } else if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            binding.emailInputLayout.setError("Enter a valid email address");
            valid = false;
        }

        if (TextUtils.isEmpty(password)) {
            binding.passwordInputLayout.setError("Password is required");
            valid = false;
        } else if (password.length() < 8) {
            binding.passwordInputLayout.setError("Password must be at least 8 characters");
            valid = false;
        }

        if (!valid) {
            return;
        }

        if (!isNetworkAvailable()) {
            showErrorMessage("No internet connection.");
            return;
        }

        setLoading(true);
        authRepository.login(new LoginRequest(email, password)).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                setLoading(false);

                if (response.isSuccessful() && response.body() != null
                        && response.body().getAccessToken() != null
                        && response.body().getUser() != null) {
                    AuthResponse body = response.body();
                    sessionManager.saveSession(
                            body.getAccessToken(),
                            body.getUser().getId(),
                            body.getRole() != null ? body.getRole() : body.getUser().getRole(),
                            body.getUser().getName()
                    );
                    openDashboard(body.getRole() != null ? body.getRole() : body.getUser().getRole());
                    return;
                }

                if (response.code() == 401) {
                    showErrorMessage("Invalid credentials. Please check your email and password.");
                    return;
                }

                showErrorMessage(parseErrorBody(response));
            }

            @Override
            public void onFailure(Call<AuthResponse> call, Throwable t) {
                setLoading(false);

                if (t instanceof IOException) {
                    showErrorMessage("Server unavailable or connection timed out.");
                    return;
                }

                showErrorMessage("An unexpected response was received from the server.");
            }
        });
    }

    private void openDashboard(String role) {
        Intent intent = NavigationUtils.dashboardIntent(this, role);
        startActivity(intent);
        finish();
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.btnLogin.setEnabled(!loading);
    }

    private void clearErrors() {
        binding.emailInputLayout.setError(null);
        binding.passwordInputLayout.setError(null);
        binding.tvError.setVisibility(View.GONE);
        binding.tvError.setText(null);
    }

    private void showErrorMessage(String message) {
        binding.tvError.setText(message);
        binding.tvError.setVisibility(View.VISIBLE);
    }

    private String parseErrorBody(Response<?> response) {
        if (response.errorBody() == null) {
            return "Login failed. Please try again.";
        }

        try {
            String error = response.errorBody().string();
            if (error != null && !error.trim().isEmpty()) {
                return "Login failed. Please check your details and try again.";
            }
        } catch (Exception ignored) {
        }

        return "Login failed. Please try again.";
    }
}
