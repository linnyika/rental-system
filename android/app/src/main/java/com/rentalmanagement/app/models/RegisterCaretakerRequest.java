package com.rentalmanagement.app.models;

import com.google.gson.annotations.SerializedName;

public class RegisterCaretakerRequest {

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

    @SerializedName("property_id")
    private final int propertyId;

    public RegisterCaretakerRequest(
            String name,
            String phone,
            String email,
            String password,
            String passwordConfirmation,
            int propertyId
    ) {
        this.name = name;
        this.phone = phone;
        this.email = email;
        this.password = password;
        this.passwordConfirmation = passwordConfirmation;
        this.propertyId = propertyId;
    }
}
