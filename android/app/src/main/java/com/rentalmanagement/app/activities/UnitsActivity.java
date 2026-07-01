package com.rentalmanagement.app.activities;

import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;

import com.rentalmanagement.app.adapters.DashboardListAdapter;
import com.rentalmanagement.app.databinding.ActivityUnitsBinding;
import com.rentalmanagement.app.models.ApiMessageResponse;
import com.rentalmanagement.app.models.PropertiesResponse;
import com.rentalmanagement.app.models.PropertyItem;
import com.rentalmanagement.app.models.UnitCreateRequest;
import com.rentalmanagement.app.models.UnitItem;
import com.rentalmanagement.app.models.UnitsResponse;
import com.rentalmanagement.app.repository.PropertyRepository;
import com.rentalmanagement.app.repository.UnitRepository;
import com.rentalmanagement.app.utilities.ApiErrorParser;
import com.rentalmanagement.app.utilities.DashboardMapper;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class UnitsActivity extends BaseDashboardActivity {

    public static final String EXTRA_PROPERTY_ID = "extra_property_id";

    private ActivityUnitsBinding binding;
    private PropertyRepository propertyRepository;
    private UnitRepository unitRepository;
    private final List<PropertyItem> properties = new ArrayList<>();
    private int selectedPropertyId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityUnitsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        propertyRepository = new PropertyRepository();
        unitRepository = new UnitRepository();

        configureToolbar(binding.toolbar, "Units", true);

        bindHeader();
        binding.rvUnits.setLayoutManager(new LinearLayoutManager(this));
        binding.rvUnits.setNestedScrollingEnabled(false);
        binding.rvUnits.setAdapter(new DashboardListAdapter(Collections.emptyList()));

        binding.swipeRefreshLayout.setOnRefreshListener(this::refreshCurrentSelection);
        binding.btnRetry.setOnClickListener(v -> refreshCurrentSelection());

        binding.spinnerProperties.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if (position < 0 || position >= properties.size()) {
                    selectedPropertyId = -1;
                    renderUnits(Collections.emptyList());
                    return;
                }
                PropertyItem item = properties.get(position);
                selectedPropertyId = item.getId() == null ? -1 : item.getId();
                loadUnits(false);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {
                selectedPropertyId = -1;
            }
        });

        binding.btnAddUnit.setOnClickListener(v -> addUnit());

        int requestedPropertyId = getIntent().getIntExtra(EXTRA_PROPERTY_ID, -1);
        if (requestedPropertyId > 0) {
            selectedPropertyId = requestedPropertyId;
        }

        loadProperties();
    }

    private void bindHeader() {
        String userName = sessionManager.getUserName();
        if (userName == null || userName.trim().isEmpty()) {
            userName = getString(com.rentalmanagement.app.R.string.app_name);
        }

        binding.tvUserName.setText(userName);
        binding.tvUserRole.setText("Units Management");
        binding.tvProfilePlaceholder.setText(initialFor(userName));
        binding.btnLogout.setOnClickListener(v -> logout());
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
                    return;
                }

                properties.clear();
                if (response.body().getProperties() != null) {
                    properties.addAll(response.body().getProperties());
                }

                if (properties.isEmpty()) {
                    showEmpty("No properties found. Add properties first.");
                    binding.spinnerProperties.setAdapter(new ArrayAdapter<>(
                            UnitsActivity.this,
                            android.R.layout.simple_spinner_item,
                            Collections.singletonList("No properties")
                    ));
                    return;
                }

                hideError();
                binding.tvEmpty.setVisibility(View.GONE);
                bindPropertySpinner();
            }

            @Override
            public void onFailure(Call<PropertiesResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load properties. Please try again.");
            }
        });
    }

    private void bindPropertySpinner() {
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

        if (selectedPropertyId > 0) {
            for (int i = 0; i < properties.size(); i++) {
                Integer id = properties.get(i).getId();
                if (id != null && id == selectedPropertyId) {
                    binding.spinnerProperties.setSelection(i);
                    return;
                }
            }
        }

        binding.spinnerProperties.setSelection(0);
    }

    private void loadUnits(boolean fromRefresh) {
        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        if (selectedPropertyId <= 0) {
            renderUnits(Collections.emptyList());
            return;
        }

        if (!fromRefresh) {
            setLoading(true);
        } else {
            binding.swipeRefreshLayout.setRefreshing(true);
        }
        hideError();

        unitRepository.units(authorization, selectedPropertyId).enqueue(new Callback<UnitsResponse>() {
            @Override
            public void onResponse(Call<UnitsResponse> call, Response<UnitsResponse> response) {
                setLoading(false);
                if (!response.isSuccessful() || response.body() == null) {
                    showError("Failed to load units. Please try again.");
                    return;
                }

                List<UnitItem> units = response.body().getUnits() == null
                        ? Collections.emptyList()
                        : response.body().getUnits();
                renderUnits(units);
            }

            @Override
            public void onFailure(Call<UnitsResponse> call, Throwable t) {
                setLoading(false);
                showError("Unable to load units. Please try again.");
            }
        });
    }

    private void addUnit() {
        clearAddUnitErrors();

        String authorization = authorizationHeader();
        if (authorization == null) {
            clearAndGoLogin();
            return;
        }

        if (selectedPropertyId <= 0) {
            Toast.makeText(this, "Select a property first.", Toast.LENGTH_SHORT).show();
            return;
        }

        String unitNumber = text(binding.etUnitNumber);
        String rentText = text(binding.etRentAmount);

        if (unitNumber.isEmpty()) {
            binding.etUnitNumber.setError("Unit number is required");
            return;
        }

        if (rentText.isEmpty()) {
            binding.etRentAmount.setError("Rent amount is required");
            return;
        }

        double rentAmount;
        try {
            rentAmount = Double.parseDouble(rentText);
        } catch (NumberFormatException ex) {
            binding.etRentAmount.setError("Enter a valid amount");
            return;
        }

        setLoading(true);
        hideError();

        unitRepository.addUnit(authorization, selectedPropertyId, new UnitCreateRequest(unitNumber, rentAmount))
                .enqueue(new Callback<ApiMessageResponse>() {
                    @Override
                    public void onResponse(Call<ApiMessageResponse> call, Response<ApiMessageResponse> response) {
                        setLoading(false);
                        if (!response.isSuccessful()) {
                            ApiErrorParser.ParsedApiError parsedError = ApiErrorParser.parse(
                                    response,
                                    "Failed to add unit. Please verify your input and try again."
                            );
                            applyAddUnitErrors(parsedError.getFieldErrors());
                            showError(parsedError.getMessage());
                            return;
                        }

                        binding.etUnitNumber.setText("");
                        binding.etRentAmount.setText("");
                        Toast.makeText(UnitsActivity.this,
                                response.body() != null && response.body().getMessage() != null
                                        ? response.body().getMessage()
                                        : "Unit added successfully",
                                Toast.LENGTH_SHORT).show();
                        loadUnits(false);
                    }

                    @Override
                    public void onFailure(Call<ApiMessageResponse> call, Throwable t) {
                        setLoading(false);
                        showError("Unable to add unit. Please try again.");
                    }
                });
    }

    private void renderUnits(List<UnitItem> units) {
        hideError();
        boolean isEmpty = units == null || units.isEmpty();
        binding.tvEmpty.setVisibility(isEmpty ? View.VISIBLE : View.GONE);
        binding.rvUnits.setVisibility(isEmpty ? View.GONE : View.VISIBLE);

        if (isEmpty) {
            binding.rvUnits.setAdapter(new DashboardListAdapter(Collections.emptyList()));
            return;
        }

        binding.rvUnits.setAdapter(new DashboardListAdapter(DashboardMapper.units(units)));
    }

    private void refreshCurrentSelection() {
        if (properties.isEmpty()) {
            loadProperties();
            return;
        }
        loadUnits(true);
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

    private void showEmpty(String message) {
        hideError();
        binding.tvEmpty.setText(message);
        binding.tvEmpty.setVisibility(View.VISIBLE);
        binding.rvUnits.setVisibility(View.GONE);
    }

    private String text(com.google.android.material.textfield.TextInputEditText input) {
        return input.getText() == null ? "" : input.getText().toString().trim();
    }

    private void clearAddUnitErrors() {
        binding.etUnitNumber.setError(null);
        binding.etRentAmount.setError(null);
    }

    private void applyAddUnitErrors(Map<String, String> fieldErrors) {
        if (fieldErrors == null || fieldErrors.isEmpty()) {
            return;
        }

        if (fieldErrors.containsKey("unit_number")) {
            binding.etUnitNumber.setError(fieldErrors.get("unit_number"));
        }
        if (fieldErrors.containsKey("rent_amount")) {
            binding.etRentAmount.setError(fieldErrors.get("rent_amount"));
        }
    }

    private String initialFor(String value) {
        if (value == null || value.trim().isEmpty()) {
            return "U";
        }
        return value.trim().substring(0, 1).toUpperCase(Locale.ROOT);
    }
}
