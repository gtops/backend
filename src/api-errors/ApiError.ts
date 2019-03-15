import { EHttpStatus } from "./EHttpStatus";

class ApiError extends Error {
	private status: number;
	private title: string;
	private code: number;

	constructor(status: number, title: string, code: number) {
		super(title);
		this.status = status;
		this.title = title;
		this.code = code;
	}
}

export const errors = {
	IncorrectUid: new ApiError(EHttpStatus.BAD, "Некорректный uid пользователя", 100),
	NotFoundUid: new ApiError(EHttpStatus.NOT_FOUND, "Пользователя с данным uid не существует", 101)
};
