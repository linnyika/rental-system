package com.rentalmanagement.app.activities;

import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Patterns;
import android.view.View;

import com.rentalmanagement.app.databinding.ActivityLandlordSignupBinding;
import com.rentalmanagement.app.models.AuthResponse;
import com.rentalmanagement.app.models.RegisterLandlordRequest;
import com.rentalmanagement.app.preferences.SessionManager;
import com.rentalmanagement.app.repository.AuthRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;
import com.rentalmanagement.app.utilities.NavigationUtils;

import java.io.IOException;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LandlordSignupActivity extends BaseActivity {

    private ActivityLandlordSignupBinding binding;
    private SessionManager sessionManager;
    private AuthRepository authRepository;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityLandlordSignupBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        sessionManager = new SessionManager(this);
        authRepository = new AuthRepository();

        binding.btnSignUp.setOnClickListener(v -> attemptSignup());
        binding.tvLoginLink.setOnClickListener(v -> finish());
    }

    private void attemptSignup() {
        clearErrors();

        String name = readText(binding.etName);
        String email = readText(binding.etEmail);
        String phone = readText(binding.etPhone);
        String password = readText(binding.etPassword);
        String passwordConfirmation = readText(binding.etPasswordConfirmation);

        boolean valid = true;

        if (TextUtils.isEmpty(name)) {
            binding.nameInputLayout.setError("Name is required");
            valid = false;
        }

        if (TextUtils.isEmpty(email)) {
            binding.emailInputLayout.setError("Email is required");
            valid = false;
        } else if (!Patterns.EMAIL_ADDRESS.matcher(email).matches()) {
            binding.emailInputLayout.setError("Enter a valid email address");
            valid = false;
        }

        if (!TextUtils.isEmpty(phone) && phone.length() < 7) {
            binding.phoneInputLayout.setError("Enter a valid phone number");
            valid = false;
        }

        if (TextUtils.isEmpty(password)) {
            binding.passwordInputLayout.setError("Password is required");
            valid = false;
        } else if (password.length() < 8) {
            binding.passwordInputLayout.setError("Password must be at least 8 characters");
            valid = false;
        }

        if (TextUtils.isEmpty(passwordConfirmation)) {
            binding.passwordConfirmInputLayout.setError("Please confirm your password");
            valid = false;
        } else if (!TextUtils.equals(password, passwordConfirmation)) {
            binding.passwordConfirmInputLayout.setError("Passwords do not match");
            valid = false;
        }

        if (!valid) {
            return;
        }

        if (!isNetworkAvailable()) {
            showErrorMessage("No internet connection.");
            return;
        }

        RegisterLandlordRequest request = new RegisterLandlordRequest(
                name,
                email,
                TextUtils.isEmpty(phone) ? null : phone,
                password,
                passwordConfirmation
        );

        setLoading(true);
        authRepository.landlordSignup(request).enqueue(new Callback<AuthResponse>() {
            @Override
            public void onResponse(Call<AuthResponse> call, Response<AuthResponse> response) {
                setLoading(false);

                if (response.isSuccessful() && response.body() != null
                        && response.body().getAccessToken() != null
                        && response.body().getUser() != null) {
                    AuthResponse body = response.body();
                    String role = body.getRole() != null ? body.getRole() : body.getUser().getRole();
                    sessionManager.saveSession(
                            body.getAccessToken(),
                            body.getUser().getId(),
                            role,
                            body.getUser().getName()
                    );
                    openDashboard(role);
                    return;
                }

                ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(response, "Sign up failed. Please try again.");
                if (parsedError.getFieldErrors().containsKey("name")) {
                    binding.nameInputLayout.setError(parsedError.getFieldErrors().get("name"));
                }
                if (parsedError.getFieldErrors().containsKey("email")) {
                    binding.emailInputLayout.setError(parsedError.getFieldErrors().get("email"));
                }
                if (parsedError.getFieldErrors().containsKey("phone")) {
                    binding.phoneInputLayout.setError(parsedError.getFieldErrors().get("phone"));
                }
                if (parsedError.getFieldErrors().containsKey("password")) {
                    binding.passwordInputLayout.setError(parsedError.getFieldErrors().get("password"));
                }
                if (parsedError.getFieldErrors().containsKey("password_confirmation")) {
                    binding.passwordConfirmInputLayout.setError(parsedError.getFieldErrors().get("password_confirmation"));
                }
                showErrorMessage(parsedError.getMessage());
            }

            @Override
            public void onFailure(Call<AuthResponse> call, Throwable t) {
                setLoading(false);

                if (t instanceof IOException) {
                    showErrorMessage("Server unavailable or connection timed out.");
                    return;
                }

                showErrorMessage("Unable to complete sign up. Please try again.");
            }
        });
    }

    private void openDashboard(String role) {
        Intent intent = NavigationUtils.dashboardIntent(this, role);
        startActivity(intent);
        finishAffinity();
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.btnSignUp.setEnabled(!loading);
    }

    private void clearErrors() {
        binding.nameInputLayout.setError(null);
        binding.emailInputLayout.setError(null);
        binding.phoneInputLayout.setError(null);
        binding.passwordInputLayout.setError(null);
        binding.passwordConfirmInputLayout.setError(null);
        binding.tvError.setVisibility(View.GONE);
        binding.tvError.setText(null);
    }

    private void showErrorMessage(String message) {
        binding.tvError.setText(message);
        binding.tvError.setVisibility(View.VISIBLE);
    }

    private String readText(com.google.android.material.textfield.TextInputEditText input) {
        return input.getText() == null ? "" : input.getText().toString().trim();
    }
}
