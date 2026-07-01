package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import com.rentalmanagement.app.databinding.ActivityRegisterCaretakerBinding;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.PropertiesResponse;
import com.rentalmanagement.app.models.PropertyItem;
import com.rentalmanagement.app.models.RegisterCaretakerRequest;
import com.rentalmanagement.app.repository.CaretakerRepository;
import com.rentalmanagement.app.repository.PropertyRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterCaretakerActivity extends BaseDashboardActivity {

    private ActivityRegisterCaretakerBinding binding;
    private CaretakerRepository caretakerRepository;
    private PropertyRepository propertyRepository;
    private final List<PropertyItem> properties = new ArrayList<>();
    private int selectedPropertyId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityRegisterCaretakerBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        caretakerRepository = new CaretakerRepository();
        propertyRepository = new PropertyRepository();

        configureToolbar(binding.toolbar, "Register Caretaker", true);

        binding.swipeRefreshLayout.setEnabled(false);
        binding.btnRegisterCaretaker.setOnClickListener(v -> registerCaretaker());
        binding.btnRetry.setOnClickListener(v -> registerCaretaker());
        binding.spinnerProperties.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (position < 0 || position >= properties.size()) {
                    selectedPropertyId = -1;
                    return;
                }
                Integer propertyId = properties.get(position).getId();
                selectedPropertyId = propertyId == null ? -1 : propertyId;
                binding.tvPropertyHint.setVisibility(View.GONE);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                selectedPropertyId = -1;
            }
        });

        loadProperties();
    }

    private void loadProperties() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        setLoading(true);
        hideError();

        propertyRepository.properties(authorization).enqueue(new Callback<PropertiesResponse>() {
            @Override
            public void onResponse(Call<PropertiesResponse> call, Response<PropertiesResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load properties. Please try again.");
                    bindPropertySpinner(Collections.emptyList());
                    return;
                }

                properties.clear();
                if (response.body().getProperties() != null) {
                    properties.addAll(response.body().getProperties());
                }
                bindPropertySpinner(properties);
            }

            @Override
            public void onFailure(Call<PropertiesResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load properties. Please try again.");
                bindPropertySpinner(Collections.emptyList());
            }
        });
    }

    private void bindPropertySpinner(List<PropertyItem> propertyItems) {
        if (propertyItems == null || propertyItems.isEmpty()) {
            selectedPropertyId = -1;
            binding.spinnerProperties.setAdapter(new ArrayAdapter<>(
                    this,
                    android.R.layout.simple_spinner_item,
                    Collections.singletonList("No properties available")
            ));
            binding.tvPropertyHint.setText("Add a property before registering a caretaker.");
            binding.tvPropertyHint.setVisibility(View.VISIBLE);
            return;
        }

        List<String> labels = new ArrayList<>();
        for (PropertyItem property : propertyItems) {
            String name = property.getName() == null ? "Property" : property.getName();
            String address = property.getAddress() == null || property.getAddress().trim().isEmpty()
                    ? ""
                    : " - " + property.getAddress();
            labels.add(name + address);
        }

        ArrayAdapter<String> adapter = new ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_item,
                labels
        );
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerProperties.setAdapter(adapter);
        binding.spinnerProperties.setSelection(0);

        Integer propertyId = propertyItems.get(0).getId();
        selectedPropertyId = propertyId == null ? -1 : propertyId;
        binding.tvPropertyHint.setVisibility(View.GONE);
    }

    private void registerCaretaker() {
        clearFieldErrors();

        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        String name = text(binding.etName);
        String phone = text(binding.etPhone);
        String email = text(binding.etEmail);
        String password = text(binding.etPassword);
        String passwordConfirmation = text(binding.etPasswordConfirmation);

        if (name.isEmpty() || phone.isEmpty() || password.isEmpty() || passwordConfirmation.isEmpty()) {
            Toast.makeText(this, "Please complete all required fields.", Toast.LENGTH_SHORT).show();
            return;
        }

        if (password.length() < 8) {
            binding.etPassword.setError("Password must be at least 8 characters.");
            return;
        }

        if (!password.equals(passwordConfirmation)) {
            binding.etPasswordConfirmation.setError("Passwords do not match.");
            return;
        }

        if (selectedPropertyId <= 0) {
            binding.tvPropertyHint.setText("Please select a property.");
            binding.tvPropertyHint.setVisibility(View.VISIBLE);
            return;
        }

        setLoading(true);
        hideError();

        RegisterCaretakerRequest request = new RegisterCaretakerRequest(
                name,
                phone,
                email.isEmpty() ? null : email,
                password,
                passwordConfirmation,
                selectedPropertyId
        );

        caretakerRepository.registerCaretaker(authorization, request).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(
                            response,
                            "Failed to register caretaker. Please verify details and try again."
                    );
                    applyFieldErrors(parsedError.getFieldErrors());
                    showError(parsedError.getMessage());
                    return;
                }

                String message = response.body() != null && response.body().getMessage() != null
                        ? response.body().getMessage()
                        : "Caretaker registered successfully";

                Toast.makeText(RegisterCaretakerActivity.this, message, Toast.LENGTH_SHORT).show();
                clearForm();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to register caretaker. Please try again.");
            }
        });
    }

    private void clearForm() {
        binding.etName.setText("");
        binding.etPhone.setText("");
        binding.etEmail.setText("");
        binding.etPassword.setText("");
        binding.etPasswordConfirmation.setText("");
        if (!properties.isEmpty()) {
            binding.spinnerProperties.setSelection(0);
            Integer propertyId = properties.get(0).getId();
            selectedPropertyId = propertyId == null ? -1 : propertyId;
        } else {
            selectedPropertyId = -1;
        }
        clearFieldErrors();
    }

    private void clearFieldErrors() {
        binding.etName.setError(null);
        binding.etPhone.setError(null);
        binding.etEmail.setError(null);
        binding.etPassword.setError(null);
        binding.etPasswordConfirmation.setError(null);
        binding.tvPropertyHint.setVisibility(View.GONE);
    }

    private void applyFieldErrors(Map<String, String> fieldErrors) {
        if (fieldErrors == null || fieldErrors.isEmpty()) {
            return;
        }

        if (fieldErrors.containsKey("name")) {
            binding.etName.setError(fieldErrors.get("name"));
        }
        if (fieldErrors.containsKey("phone")) {
            binding.etPhone.setError(fieldErrors.get("phone"));
        }
        if (fieldErrors.containsKey("email")) {
            binding.etEmail.setError(fieldErrors.get("email"));
        }
        if (fieldErrors.containsKey("password")) {
            binding.etPassword.setError(fieldErrors.get("password"));
        }
        if (fieldErrors.containsKey("password_confirmation")) {
            binding.etPasswordConfirmation.setError(fieldErrors.get("password_confirmation"));
        }
        if (fieldErrors.containsKey("property_id")) {
            binding.tvPropertyHint.setText(fieldErrors.get("property_id"));
            binding.tvPropertyHint.setVisibility(View.VISIBLE);
        }
    }

    private String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
    }

    private void showError(String message) {
        binding.cardError.setVisibility(View.VISIBLE);
        binding.tvErrorMessage.setText(message);
    }

    private void hideError() {
        binding.cardError.setVisibility(View.GONE);
    }

    private String text(com.google.android.material.textfield.TextInputEditText input) {
        return input.getText() == null ? "" : input.getText().toString().trim();
    }
}
