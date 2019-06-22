import { config } from "@config/Config";
import * as nodemailer from "nodemailer";
import { SendMailOptions, Transporter } from "nodemailer";

export class EmailServices {
	public static transporter: Transporter = nodemailer.createTransport(config.email.SMTPData);

	public static send(options: SendMailOptions): Promise<any> {
		if (!options.text && !options.html) {
			throw new Error("Can't send email with empty body");
		}
		return EmailServices.transporter.sendMail(options);
	}

}
