package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class RegisterTenantRequest {

    @SerializedName("name")
    private final String name;

    @SerializedName("phone")
    private final String phone;

    @SerializedName("email")
    private final String email;

    @SerializedName("password")
    private final String password;

    @SerializedName("password_confirmation")
    private final String passwordConfirmation;

    @SerializedName("unit_id")
    private final int unitId;

    @SerializedName("start_date")
    private final String startDate;

    public RegisterTenantRequest(
            String name,
            String phone,
            String email,
            String password,
            String passwordConfirmation,
            int unitId,
            String startDate
    ) {
        this.name = name;
        this.phone = phone;
        this.email = email;
        this.password = password;
        this.passwordConfirmation = passwordConfirmation;
        this.unitId = unitId;
        this.startDate = startDate;
    }
}
