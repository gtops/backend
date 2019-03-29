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
	NotFoundParticipantUid: new ApiError(EHttpStatus.NOT_FOUND, "Участника с данным uid не существует", 101),

	UnknownController: new ApiError(EHttpStatus.INTERNAL, "Задан неизвестный контроллер", 500),
	NotAssignedRouteMethod: new ApiError(EHttpStatus.INTERNAL, "Неопределённый метод роута", 501),
	UnknownRouteHandle: new ApiError(EHttpStatus.INTERNAL, "Неизвестный обработчик маршрута", 502),
};
