package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class RegisterLandlordRequest {

    @SerializedName("name")
    private final String name;

    @SerializedName("email")
    private final String email;

    @SerializedName("phone")
    private final String phone;

    @SerializedName("password")
    private final String password;

    @SerializedName("password_confirmation")
    private final String passwordConfirmation;

    public RegisterLandlordRequest(
            String name,
            String email,
            String phone,
            String password,
            String passwordConfirmation
    ) {
        this.name = name;
        this.email = email;
        this.phone = phone;
        this.password = password;
        this.passwordConfirmation = passwordConfirmation;
    }
}
