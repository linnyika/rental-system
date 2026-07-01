package com.rentalmanagement.app.activities;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import com.rentalmanagement.app.databinding.ActivityRegisterTenantBinding;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.PropertiesResponse;
import com.rentalmanagement.app.models.PropertyItem;
import com.rentalmanagement.app.models.PropertyLite;
import com.rentalmanagement.app.models.RegisterTenantRequest;
import com.rentalmanagement.app.models.UnitItem;
import com.rentalmanagement.app.models.UnitsResponse;
import com.rentalmanagement.app.repository.PropertyRepository;
import com.rentalmanagement.app.repository.TenantRepository;
import com.rentalmanagement.app.repository.UnitRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Collections;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RegisterTenantActivity extends BaseDashboardActivity {

    private ActivityRegisterTenantBinding binding;
    private PropertyRepository propertyRepository;
    private UnitRepository unitRepository;
    private TenantRepository tenantRepository;

    private final List<PropertyItem> properties = new ArrayList<>();
    private final List<UnitItem> allAvailableUnits = new ArrayList<>();
    private final List<UnitItem> filteredUnits = new ArrayList<>();

    private int selectedPropertyId = -1;
    private int selectedUnitId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityRegisterTenantBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        propertyRepository = new PropertyRepository();
        unitRepository = new UnitRepository();
        tenantRepository = new TenantRepository();

        configureToolbar(binding.toolbar, "Register Tenant", true);

        bindHeader();

        binding.swipeRefreshLayout.setOnRefreshListener(this::loadData);
        binding.btnRetry.setOnClickListener(v -> loadData());
        binding.btnRegisterTenant.setOnClickListener(v -> registerTenant());

        binding.etStartDate.setOnClickListener(v -> showDatePicker());

        binding.spinnerProperties.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (position < 0 || position >= properties.size()) {
                    selectedPropertyId = -1;
                    bindUnitSpinner(Collections.emptyList());
                    return;
                }

                PropertyItem property = properties.get(position);
                selectedPropertyId = property.getId() == null ? -1 : property.getId();
                filterUnitsByProperty();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                selectedPropertyId = -1;
                bindUnitSpinner(Collections.emptyList());
            }
        });

        binding.spinnerUnits.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (position < 0 || position >= filteredUnits.size()) {
                    selectedUnitId = -1;
                    return;
                }

                Integer unitId = filteredUnits.get(position).getId();
                selectedUnitId = unitId == null ? -1 : unitId;
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                selectedUnitId = -1;
            }
        });

        loadData();
    }

    private void bindHeader() {
        String userName = sessionManager.getUserName();
        if (userName == null || userName.trim().isEmpty()) {
            userName = getString(com.rentalmanagement.app.R.string.app_name);
        }

        binding.tvUserName.setText(userName);
        binding.tvUserRole.setText("Tenant Registration");
        binding.tvProfilePlaceholder.setText(initialFor(userName));
        binding.btnLogout.setOnClickListener(v -> logout());
    }

    private void loadData() {
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
                if (!response.isSuccessful() || response.body() == null) {
                    setLoading(false);
                    showError("Failed to load properties. Please try again.");
                    return;
                }

                properties.clear();
                if (response.body().getProperties() != null) {
                    properties.addAll(response.body().getProperties());
                }
                bindPropertySpinner();
                loadAvailableUnits();
            }

            @Override
            public void onFailure(Call<PropertiesResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load properties. Please try again.");
            }
        });
    }

    private void loadAvailableUnits() {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        unitRepository.availableUnits(authorization).enqueue(new Callback<UnitsResponse>() {
            @Override
            public void onResponse(Call<UnitsResponse> call, Response<UnitsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load available units. Please try again.");
                    return;
                }

                allAvailableUnits.clear();
                if (response.body().getUnits() != null) {
                    allAvailableUnits.addAll(response.body().getUnits());
                }

                hideError();
                filterUnitsByProperty();
            }

            @Override
            public void onFailure(Call<UnitsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load available units. Please try again.");
            }
        });
    }

    private void bindPropertySpinner() {
        if (properties.isEmpty()) {
            binding.spinnerProperties.setAdapter(new ArrayAdapter<>(
                    this,
                    android.R.layout.simple_spinner_item,
                    Collections.singletonList("No properties")
            ));
            selectedPropertyId = -1;
            return;
        }

        List<String> labels = new ArrayList<>();
        for (PropertyItem property : properties) {
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
        Integer firstPropertyId = properties.get(0).getId();
        selectedPropertyId = firstPropertyId == null ? -1 : firstPropertyId;
    }

    private void filterUnitsByProperty() {
        filteredUnits.clear();

        if (selectedPropertyId <= 0 || allAvailableUnits.isEmpty()) {
            bindUnitSpinner(Collections.emptyList());
            return;
        }

        for (UnitItem unit : allAvailableUnits) {
            PropertyLite property = unit.getProperty();
            Integer propertyId = property == null ? null : property.getId();
            if (propertyId != null && propertyId == selectedPropertyId) {
                filteredUnits.add(unit);
            }
        }

        bindUnitSpinner(filteredUnits);
    }

    private void bindUnitSpinner(List<UnitItem> units) {
        if (units == null || units.isEmpty()) {
            selectedUnitId = -1;
            binding.spinnerUnits.setAdapter(new ArrayAdapter<>(
                    this,
                    android.R.layout.simple_spinner_item,
                    Collections.singletonList("No available units")
            ));
            binding.tvUnitHint.setText("No available units for the selected property.");
            binding.tvUnitHint.setVisibility(View.VISIBLE);
            return;
        }

        List<String> labels = new ArrayList<>();
        for (UnitItem unit : units) {
            String unitNo = unit.getUnitNumber() == null ? "-" : unit.getUnitNumber();
            long rent = Math.round(unit.getRentAmount() == null ? 0 : unit.getRentAmount());
            labels.add("Unit " + unitNo + " (KES " + rent + ")");
        }

        ArrayAdapter<String> adapter = new ArrayAdapter<>(
                this,
                android.R.layout.simple_spinner_item,
                labels
        );
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        binding.spinnerUnits.setAdapter(adapter);
        binding.spinnerUnits.setSelection(0);

        Integer firstUnitId = units.get(0).getId();
        selectedUnitId = firstUnitId == null ? -1 : firstUnitId;
        binding.tvUnitHint.setVisibility(View.GONE);
    }

    private void registerTenant() {
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
        String startDate = text(binding.etStartDate);

        if (name.isEmpty() || phone.isEmpty() || password.isEmpty() || passwordConfirmation.isEmpty() || startDate.isEmpty()) {
            Toast.makeText(this, "Please complete all required fields.", Toast.LENGTH_SHORT).show();
            return;
        }

        if (selectedPropertyId <= 0 || selectedUnitId <= 0) {
            Toast.makeText(this, "Please select a property and available unit.", Toast.LENGTH_SHORT).show();
            return;
        }

        setLoading(true);
        hideError();

        RegisterTenantRequest request = new RegisterTenantRequest(
                name,
                phone,
                email.isEmpty() ? null : email,
                password,
                passwordConfirmation,
                selectedUnitId,
                startDate
        );

        tenantRepository.registerTenant(authorization, request).enqueue(new Callback<ApiMessageResponse>() {
            @Override
            public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                setLoading(false);
                if (!response.isSuccessful()) {
                    ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(
                            response,
                            "Failed to register tenant. Please verify details and try again."
                    );
                    applyFieldErrors(parsedError.getFieldErrors());
                    showError(parsedError.getMessage());
                    return;
                }

                String message = response.body() != null && response.body().getMessage() != null
                        ? response.body().getMessage()
                        : "Tenant registered successfully";

                Toast.makeText(RegisterTenantActivity.this, message, Toast.LENGTH_SHORT).show();
                clearForm();
                loadAvailableUnits();
            }

            @Override
            public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to register tenant. Please try again.");
            }
        });
    }

    private void showDatePicker() {
        Calendar calendar = Calendar.getInstance();
        DatePickerDialog dialog = new DatePickerDialog(
                this,
                (view, year, month, dayOfMonth) -> binding.etStartDate.setText(
                        String.format(Locale.US, "%04d-%02d-%02d", year, month + 1, dayOfMonth)
                ),
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        );
        dialog.show();
    }

    private void clearForm() {
        binding.etName.setText("");
        binding.etPhone.setText("");
        binding.etEmail.setText("");
        binding.etPassword.setText("");
        binding.etPasswordConfirmation.setText("");
        binding.etStartDate.setText("");
        clearFieldErrors();
    }

    private void clearFieldErrors() {
        binding.etName.setError(null);
        binding.etPhone.setError(null);
        binding.etEmail.setError(null);
        binding.etPassword.setError(null);
        binding.etPasswordConfirmation.setError(null);
        binding.etStartDate.setError(null);
        binding.tvUnitHint.setVisibility(View.GONE);
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
        if (fieldErrors.containsKey("start_date")) {
            binding.etStartDate.setError(fieldErrors.get("start_date"));
        }
        if (fieldErrors.containsKey("unit_id")) {
            binding.tvUnitHint.setText(fieldErrors.get("unit_id"));
            binding.tvUnitHint.setVisibility(View.VISIBLE);
        }
    }

    private String authorizationHeader() {
        String token = sessionManager.getToken();
        return token == null || token.trim().isEmpty() ? null : "Bearer " + token;
    }

    private void setLoading(boolean loading) {
        binding.progressBar.setVisibility(loading ? View.VISIBLE : View.GONE);
        binding.swipeRefreshLayout.setRefreshing(loading);
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

    private String initialFor(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "U";
        }
        return value.trim().substring(0, 1).toUpperCase(Locale.ROOT);
    }
}
